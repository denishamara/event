<?php
namespace App\Controllers;

use App\Models\RefundModel;
use App\Models\EventTicketModel;
use App\Models\EventModel;

class AdminRefundController extends BaseController
{
    protected $refund;
    protected $ticket;
    protected $event;
    protected $db;

    public function __construct()
    {
        $this->refund = new RefundModel();
        $this->ticket = new EventTicketModel();
        $this->event  = new EventModel();
        $this->db      = \Config\Database::connect();
    }

    public function index()
    {
    if (session()->get('role') !== 'admin') {
        return redirect()->to('/dashboard');
    }

    $refunds = $this->db->table('refunds')
        ->select('
            refunds.*,
            users.name AS user_name,
            users.email AS user_email,
            events.title AS event_title,
            event_tickets.ticket_code
        ')
        ->join('event_tickets', 'event_tickets.id = refunds.ticket_id')
        ->join('users', 'users.id = refunds.user_id')
        ->join('events', 'events.id = event_tickets.event_id')
        ->orderBy('refunds.created_at', 'DESC')
        ->get()
        ->getResultArray();

    // 🔥 GANTI PATH VIEW DI SINI
    return view('admin/payments/refunds', [
        'refunds' => $refunds
    ]);
    }
    public function approve($id)
    {
        $refund = $this->refund->find($id);
        if (!$refund) return redirect()->back();

        $ticket = $this->ticket->find($refund['ticket_id']);
        if (!$ticket) return redirect()->back();

        $this->db->transStart();

        // 1. Update refund status → approved
        $this->refund->update($id, [
            'status' => 'approved',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // 2. Mark ticket as refunded
        $this->ticket->update($ticket['id'], [
            'status' => 'refunded'
        ]);

        // 3. Return quota based on event type (sesuai qty tiket)
        $qtyToReturn = (int)($ticket['qty'] ?? 1);
        
        if (($ticket['phase'] ?? 'Regular') === 'Regular') {
            // Regular Event - return to events.quota
            $this->db->table('events')
                ->where('id', $ticket['event_id'])
                ->set('quota', 'quota + ' . $qtyToReturn, false)
                ->update();
        } else {
            // Phase Event - return to event_prices.quota_remaining
            $this->db->table('event_prices')
                ->where('event_id', $ticket['event_id'])
                ->where('phase', $ticket['phase'])
                ->set('quota_remaining', 'quota_remaining + ' . $qtyToReturn, false)
                ->update();
        }

        // 4. Update payment status if this was the last active ticket
        if ($ticket['payment_id']) {
            $remainingActiveTickets = $this->ticket
                ->where('payment_id', $ticket['payment_id'])
                ->whereIn('status', ['paid', 'pending'])
                ->countAllResults();

            if ($remainingActiveTickets == 0) {
                $this->db->table('payments')
                    ->where('id', $ticket['payment_id'])
                    ->update(['status' => 'refunded']);
            }
        }

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return redirect()->back()->with('error', 'Failed to approve refund');
        }

        return redirect()->back()->with('success', 'Refund approved and quota returned');
    }

    public function reject($id)
    {
        $refund = $this->refund->find($id);
        if (!$refund) return redirect()->back();

        $this->db->transStart();

        // Update refund status → rejected
        $this->refund->update($id, [
            'status' => 'rejected',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Restore ticket to active ('paid') when refund is rejected
        if (!empty($refund['ticket_id'])) {
            $this->ticket->update($refund['ticket_id'], [
                'status' => 'paid'
            ]);
        }

        $this->db->transComplete();

        return redirect()->back()->with('success', 'Refund rejected, ticket restored');
    }

    public function show($id)
    {
    if (session()->get('role') !== 'admin') {
        return redirect()->to('/dashboard');
    }

        $refund = $this->db->table('refunds')
            ->select('
                refunds.*,
                users.name AS user_name,
                users.email AS user_email,
                events.title AS event_title,
                event_tickets.ticket_code
            ')
            ->join('event_tickets', 'event_tickets.id = refunds.ticket_id')
            ->join('users', 'users.id = refunds.user_id')
            ->join('events', 'events.id = event_tickets.event_id')
            ->where('refunds.id', $id)
            ->get()
            ->getRowArray();

    if (!$refund) {
        return redirect()->to('/admin/refunds');
    }

    return view('admin/payments/refund_review', [
        'refund' => $refund
    ]);
    }
}
