<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdminReportController extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $db = \Config\Database::connect();
        $search = $this->request->getGet('q');

        /* =====================================================
           EVENT PERFORMANCE (VALID SOURCE)
        ===================================================== */
        $builder = $db->table('events e')
            ->select('
                e.id,
                e.title,
                e.event_date,

                (
                    SELECT COALESCE(SUM(ep.quota_remaining),0)
                    FROM event_prices ep
                    WHERE ep.event_id = e.id
                ) AS remaining,

                (
                    SELECT COALESCE(SUM(t.qty),0)
                    FROM event_tickets t
                    WHERE t.event_id = e.id
                    AND t.status = "paid"
                ) AS sold,

                (
                    SELECT COALESCE(SUM(p.amount),0)
                    FROM payments p
                    JOIN event_tickets t2 ON t2.id = p.event_ticket_id
                    WHERE t2.event_id = e.id
                    AND p.status = "paid"
                ) - (
                    SELECT COALESCE(SUM(t3.price),0)
                    FROM event_tickets t3
                    JOIN refunds r ON r.ticket_id = t3.id
                    WHERE t3.event_id = e.id
                    AND r.status = "approved"
                ) AS revenue
            ')
            ->orderBy('e.event_date','DESC');

        if ($search) {
            $builder->like('e.title', $search);
        }

        $events = $builder->get()->getResultArray();

        /* =====================================================
           SUMMARY
        ===================================================== */
        $totalRevenue = 0;
        $totalSold = 0;
        $totalRemaining = 0;

        foreach ($events as &$e) {
            $e['remaining'] = (int)$e['remaining'];
            $e['sold'] = (int)$e['sold'];
            $e['revenue'] = (int)$e['revenue'];
            $e['ended'] = strtotime($e['event_date']) < time();

            $totalRevenue += $e['revenue'];
            $totalSold += $e['sold'];
            $totalRemaining += $e['remaining'];
        }

        /* =====================================================
           TRANSACTION DETAIL - Initial 50
        ===================================================== */
        $transactionSearch = $this->request->getGet('t_search');
        $transactionEvent = $this->request->getGet('t_event');
        $transactionMethod = $this->request->getGet('t_method');
        $transactionStatus = $this->request->getGet('t_status');
        $transactionDateFrom = $this->request->getGet('t_date_from');
        $transactionDateTo = $this->request->getGet('t_date_to');

        $allTransactions = $this->getTransactions($db, 50, 0, $transactionSearch, $transactionEvent, $transactionMethod, $transactionStatus, $transactionDateFrom, $transactionDateTo);

        // Get all events for filter dropdown
        $allEvents = $db->table('events')
            ->select('id, title')
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/reports/index', [
            'events'              => $events,
            'transactions'        => $allTransactions,
            'search'              => $search,
            'totalRevenue'        => $totalRevenue,
            'totalSold'           => $totalSold,
            'totalRemaining'      => $totalRemaining,
            'allEvents'           => $allEvents,
            'transactionSearch'   => $transactionSearch,
            'transactionEvent'    => $transactionEvent,
            'transactionMethod'   => $transactionMethod,
            'transactionStatus'   => $transactionStatus,
            'transactionDateFrom' => $transactionDateFrom,
            'transactionDateTo'   => $transactionDateTo
        ]);
    }

    public function loadMoreTransactions()
    {
        if (session()->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        
        $offset = (int)$this->request->getGet('offset');
        $transactionSearch = $this->request->getGet('t_search');
        $transactionEvent = $this->request->getGet('t_event');
        $transactionMethod = $this->request->getGet('t_method');
        $transactionStatus = $this->request->getGet('t_status');
        $transactionDateFrom = $this->request->getGet('t_date_from');
        $transactionDateTo = $this->request->getGet('t_date_to');

        // Get 51 to check if there's more
        $transactions = $this->getTransactions($db, 51, $offset, $transactionSearch, $transactionEvent, $transactionMethod, $transactionStatus, $transactionDateFrom, $transactionDateTo);
        
        $hasMore = count($transactions) > 50;
        if ($hasMore) {
            $transactions = array_slice($transactions, 0, 50);
        }

        return $this->response->setJSON([
            'success' => true,
            'transactions' => $transactions,
            'hasMore' => $hasMore
        ]);
    }

    private function getTransactions($db, $limit, $offset, $search = null, $event = null, $method = null, $status = null, $dateFrom = null, $dateTo = null)
{
    $transactionBuilder = $db->table('event_tickets t')
        ->select('
            p.method,
            (t.price * t.qty) as amount,
            t.qty,
            p.status,
            p.created_at,
            t.ticket_code,
            e.title AS event_title,
            e.id AS event_id,
            "payment" as type
        ')
        ->join('payments p','p.id = t.payment_id')
        ->join('events e','e.id = t.event_id')
        ->where('t.status', 'paid');

    if ($search) {
        $transactionBuilder->groupStart()
            ->like('t.ticket_code', $search)
            ->orLike('e.title', $search)
            ->groupEnd();
    }

    if ($event) {
        $transactionBuilder->where('e.id', $event);
    }

    if ($method) {
        $transactionBuilder->where('p.method', $method);
    }

    if ($status) {
        $transactionBuilder->where('p.status', $status);
    }

    if ($dateFrom) {
        $transactionBuilder->where('DATE(p.created_at) >=', $dateFrom);
    }

    if ($dateTo) {
        $transactionBuilder->where('DATE(p.created_at) <=', $dateTo);
    }

    $transactions = $transactionBuilder
        ->orderBy('p.created_at','DESC')
        ->get()
        ->getResultArray();


    /* ================= REFUND ================= */

    $refundBuilder = $db->table('refunds r')
        ->select('
            r.status,
            r.created_at,
            t.ticket_code,
            (t.price * t.qty) as amount,
            t.qty,
            e.title AS event_title,
            e.id AS event_id,
            r.reason,
            "refund" as method,
            "refund" as type
        ')
        ->join('event_tickets t','t.id = r.ticket_id')
        ->join('events e','e.id = t.event_id');

    if ($search) {
        $refundBuilder->groupStart()
            ->like('t.ticket_code', $search)
            ->orLike('e.title', $search)
            ->groupEnd();
    }

    if ($event) {
        $refundBuilder->where('e.id', $event);
    }

    if ($dateFrom) {
        $refundBuilder->where('DATE(r.created_at) >=', $dateFrom);
    }

    if ($dateTo) {
        $refundBuilder->where('DATE(r.created_at) <=', $dateTo);
    }

    $refunds = $refundBuilder
        ->orderBy('r.created_at','DESC')
        ->get()
        ->getResultArray();

    foreach ($refunds as &$refund) {
        $refund['amount'] = -1 * $refund['amount'];
    }

    $allTransactions = array_merge($transactions, $refunds);

    usort($allTransactions, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    return array_slice($allTransactions, $offset, $limit);
}

    public function exportPdf()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $db = \Config\Database::connect();
        $search = $this->request->getGet('q');

        /* =====================================================
           EVENT PERFORMANCE
        ===================================================== */
        $builder = $db->table('events e')
            ->select('
                e.id,
                e.title,
                e.event_date,

                (
                    SELECT COALESCE(SUM(ep.quota_remaining),0)
                    FROM event_prices ep
                    WHERE ep.event_id = e.id
                ) AS remaining,

                (
                    SELECT COUNT(t.id)
                    FROM event_tickets t
                    WHERE t.event_id = e.id
                    AND t.status = "paid"
                ) AS sold,

                (
                    SELECT COALESCE(SUM(p.amount),0)
                    FROM payments p
                    JOIN event_tickets t2 ON t2.id = p.event_ticket_id
                    WHERE t2.event_id = e.id
                    AND p.status = "paid"
                ) - (
                    SELECT COALESCE(SUM(t3.price),0)
                    FROM refunds r
                    JOIN event_tickets t3 ON t3.id = r.ticket_id
                    WHERE t3.event_id = e.id
                    AND r.status = "approved"
                ) AS revenue
            ')
            ->orderBy('e.event_date','DESC');

        if ($search) {
            $builder->like('e.title', $search);
        }

        $events = $builder->get()->getResultArray();

        /* =====================================================
           SUMMARY
        ===================================================== */
        $totalRevenue = 0;
        $totalSold = 0;
        $totalRemaining = 0;

        foreach ($events as &$e) {
            $e['remaining'] = (int)$e['remaining'];
            $e['sold'] = (int)$e['sold'];
            $e['revenue'] = (int)$e['revenue'];
            $e['ended'] = strtotime($e['event_date']) < time();

            $totalRevenue += $e['revenue'];
            $totalSold += $e['sold'];
            $totalRemaining += $e['remaining'];
        }

        /* =====================================================
           TRANSACTION DETAIL - All for PDF
        ===================================================== */
        $transactionSearch = $this->request->getGet('t_search');
        $transactionEvent = $this->request->getGet('t_event');
        $transactionMethod = $this->request->getGet('t_method');
        $transactionStatus = $this->request->getGet('t_status');
        $transactionDateFrom = $this->request->getGet('t_date_from');
        $transactionDateTo = $this->request->getGet('t_date_to');

        // Get all transactions for PDF (no limit)
        $allTransactions = $this->getTransactions($db, 999999, 0, $transactionSearch, $transactionEvent, $transactionMethod, $transactionStatus, $transactionDateFrom, $transactionDateTo);

        // Generate PDF
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Get event name if filter applied
        $transactionEventName = '';
        if ($transactionEvent) {
            $eventData = $db->table('events')->where('id', $transactionEvent)->get()->getRow();
            if ($eventData) {
                $transactionEventName = $eventData->title;
            }
        }
        
        $html = view('admin/reports/pdf', [
            'events'                => $events,
            'transactions'          => $allTransactions,
            'totalRevenue'          => $totalRevenue,
            'totalSold'             => $totalSold,
            'totalRemaining'        => $totalRemaining,
            'generatedDate'         => date('d F Y H:i'),
            'transactionSearch'     => $transactionSearch,
            'transactionEventName'  => $transactionEventName,
            'transactionMethod'     => $transactionMethod,
            'transactionStatus'     => $transactionStatus,
            'transactionDateFrom'   => $transactionDateFrom,
            'transactionDateTo'     => $transactionDateTo
        ]);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $filename = 'Sales_Report_' . date('YmdHis') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }
}
