<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\EventTicketModel;
use App\Models\EventModel;
use App\Models\EventPriceModel;
use App\Models\UserModel;

class AdminPaymentController extends BaseController
{
    protected $payment;
    protected $ticket;
    protected $event;
    protected $price;
    protected $db;

    public function __construct()
    {
        $this->payment = new PaymentModel();
        $this->ticket  = new EventTicketModel();
        $this->event   = new EventModel();
        $this->price   = new EventPriceModel();
        $this->db      = \Config\Database::connect();
    }

    /* ===============================
        PENDING PAYMENT
    =============================== */
    public function pending()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $payments = $this->db->table('payments')
            ->distinct()
            ->select('
                payments.id AS payment_id,
                payments.status,
                payments.amount,
                payments.qty,
                payments.method,
                payments.created_at,
                payments.proof_file,
                payments.event_ticket_id,
                users.name AS user_name,
                events.title AS event_title,
                events.image AS event_image
            ')
            ->join('event_tickets', 'event_tickets.payment_id = payments.id')
            ->join('users', 'users.id = event_tickets.user_id')
            ->join('events', 'events.id = event_tickets.event_id')
            ->where('payments.status', 'pending')
            ->orderBy('payments.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/payments/pending', [
            'payments' => $payments
        ]);
    }

    /* ===============================
        VIEW PROOF
    =============================== */
    public function viewProof($paymentId)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $payment = $this->payment->find($paymentId);

        if (!$payment || empty($payment['proof_file'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Proof not found');
        }

        $filePath = WRITEPATH . 'uploads/proofs/' . $payment['proof_file'];

        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        $mime = mime_content_type($filePath);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Length', filesize($filePath))
            ->setBody(file_get_contents($filePath));
    }

    /* ===============================
        APPROVE
    =============================== */
    public function approve($paymentId)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $payment = $this->payment->find($paymentId);

        if (!$payment || $payment['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Invalid payment');
        }

        if (empty($payment['event_ticket_id'])) {
            return redirect()->back()->with('error', 'Payment missing ticket reference');
        }

        $firstTicket = $this->ticket->find($payment['event_ticket_id']);

        if (!$firstTicket) {
            return redirect()->back()->with('error', 'Ticket reference not found');
        }

        $qty = (int) $payment['qty'];

        $this->db->transStart();

        $this->payment->update($paymentId, [
            'status' => 'paid'
        ]);

        $tickets = $this->ticket
            ->where('payment_id', $paymentId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'ASC')
            ->findAll();

        if (empty($tickets)) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Ticket not found');
        }

        $totalTicketQty = 0;
        foreach ($tickets as $t) {
            $totalTicketQty += (int)($t['qty'] ?? 1);
        }

        if ($totalTicketQty !== $qty) {
            $this->db->transRollback();
            return redirect()->back()->with(
                'error',
                'Ticket quantity mismatch (payment qty: ' . $qty .
                ', ticket total qty: ' . $totalTicketQty .
                ', ticket count: ' . count($tickets) . ')'
            );
        }

        foreach ($tickets as $ticket) {
            $qrData = 'TKT-' . $ticket['id'] . '-' . uniqid() . '-' .
                      hash('sha256', $ticket['ticket_code'] . time());

            $this->ticket->update($ticket['id'], [
                'status'  => 'paid',
                'qr_data' => $qrData
            ]);
        }

        $firstTicket = $tickets[0];

        if (($firstTicket['phase'] ?? 'Regular') === 'Regular') {

            $this->db->table('events')
                ->where('id', $firstTicket['event_id'])
                ->set('quota', 'quota - ' . $qty, false)
                ->update();

        } else {

            $this->db->table('event_prices')
                ->where('event_id', $firstTicket['event_id'])
                ->where('phase', $firstTicket['phase'])
                ->set('quota_remaining', 'quota_remaining - ' . $qty, false)
                ->update();
        }

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return redirect()->back()->with('error', 'Failed approve payment');
        }

        $user  = (new UserModel())->find($firstTicket['user_id']);
        $event = $this->event->find($firstTicket['event_id']);

        if ($user && !empty($user['email'])) {

            $html = view('tickets/email_ticket', [
                'tickets' => $tickets,
                'event'   => $event,
                'user'    => $user,
                'qty'     => $qty
            ]);

            $email = \Config\Services::email();
            $email->setFrom('no-reply@eventapp.com', 'Event App');
            $email->setTo($user['email']);
            $email->setSubject('🎫 Ticket Event - ' . $event['title']);
            $email->setMessage($html);
            $email->setMailType('html');
            $email->send();
        }

        return redirect()->back()->with('success', 'Payment approved & email sent');
    }

    /* ===============================
        REJECT
    =============================== */
    public function reject($paymentId)
    {
        $payment = $this->payment->find($paymentId);

        if (!$payment || $payment['status'] !== 'pending') {
            return redirect()->back()->with('error','Invalid payment');
        }

        $tickets = $this->ticket
            ->where('payment_id', $paymentId)
            ->where('status','pending')
            ->findAll();

        $this->db->transStart();

        $this->payment->update($paymentId, ['status'=>'rejected']);

        foreach ($tickets as $ticket) {
            $this->ticket->update($ticket['id'], ['status'=>'rejected']);
        }

        $this->db->transComplete();

        return redirect()->back()->with('success','Payment rejected');
    }
}
