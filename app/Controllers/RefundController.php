<?php

namespace App\Controllers;

use App\Models\RefundModel;
use App\Models\EventTicketModel;
use App\Models\EventModel;

class RefundController extends BaseController
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
        $this->db     = \Config\Database::connect();
    }

    /* ===============================
       USER REQUEST REFUND
    =============================== */
    public function request()
    {
        if (!session()->get('isLogin')) {
            return redirect()->to('/login');
        }

        $ticketId = $this->request->getPost('ticket_id');
        $reason   = $this->request->getPost('refund_reason');

        $ticket = $this->ticket
            ->where('id', $ticketId)
            ->where('user_id', session()->get('user_id'))
            ->where('status', 'paid')
            ->first();

        if (!$ticket) {
            return redirect()->back()->with('error', 'Ticket not eligible for refund');
        }

        // Cek apakah event minimal H-2 (harus > 2 hari, bukan tepat 2 hari)
        $event = $this->event->find($ticket['event_id']);
        if ($event) {
            $eventDate = new \DateTime($event['event_date']);
            $today = new \DateTime();
            $daysDiff = $today->diff($eventDate)->days;
            $isBeforeEvent = $today < $eventDate;
            
            if (!$isBeforeEvent || $daysDiff <= 2) {
                return redirect()->back()->with('error', 'Refund hanya dapat dilakukan lebih dari H-2 sebelum event');
            }
        }

        $this->refund->insert([
            'ticket_id' => $ticketId,
            'user_id'   => session()->get('user_id'),
            'reason'    => $reason,
            'status'    => 'pending'
        ]);

        // mark ticket status so user sees refund requested
        $this->ticket->update($ticketId, [
            'status' => 'refund_requested'
        ]);

        return redirect()->back()->with('success', 'Refund request submitted');
    }

    /* ===============================
       ADMIN VIEW REFUNDS
    =============================== */
    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $refunds = $this->refund
            ->select('refunds.*, users.name as user_name, events.title as event_title')
            ->join('event_tickets', 'event_tickets.id = refunds.ticket_id')
            ->join('users', 'users.id = refunds.user_id')
            ->join('events', 'events.id = event_tickets.event_id')
            ->orderBy('refunds.created_at', 'DESC')
            ->findAll();

        return view('admin/payments/refunds', [
            'refunds' => $refunds
        ]);
    }

    public function review($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $refund = $this->refund->find($id);
        if (!$refund) {
            return redirect()->to('/admin/refunds');
        }

        return view('admin/payments/refund_review', [
            'refund' => $refund
        ]);
    }

    public function approve($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $refund = $this->refund->find($id);
        if (!$refund || $refund['status'] !== 'pending') {
            return redirect()->back();
        }

        $this->db->transStart();

        // update refund
        $this->refund->update($id, [
            'status' => 'approved'
        ]);

        // update ticket
        $this->ticket->update($refund['ticket_id'], [
            'status' => 'unpaid'
        ]);

        // return quota (sesuai qty tiket)
        $ticket = $this->ticket->find($refund['ticket_id']);
        $event  = $this->event->find($ticket['event_id']);
        $qtyToReturn = (int)($ticket['qty'] ?? 1);

        $this->event->update($event['id'], [
            'quota' => $event['quota'] + $qtyToReturn
        ]);

        $this->db->transComplete();

        return redirect()->to('/admin/refunds')
            ->with('success', 'Refund approved');
    }

    public function reject($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $this->refund->update($id, [
            'status' => 'rejected'
        ]);

        return redirect()->to('/admin/refunds')
            ->with('success', 'Refund rejected');
    }
}
