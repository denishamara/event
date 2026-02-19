<?php

namespace App\Controllers;

use App\Models\EventTicketModel;
use App\Models\EventModel;

class PurchaseController extends BaseController
{
    protected $ticket;
    protected $event;
    protected $db;

    public function __construct()
    {
        $this->ticket = new EventTicketModel();
        $this->event  = new EventModel();
        $this->db     = \Config\Database::connect();
    }

    public function buy($eventId)
    {
        if (!session()->get('isLogin')) {
            return redirect()->to('/login');
        }

        // ambil event
        $event = $this->event->find($eventId);

        if (!$event) {
            return redirect()->to('/events');
        }

        // cek quota
        if ($event['quota'] <= 0) {
            return redirect()->back()->with('error', 'Quota event sudah habis');
        }

        // ================= TRANSACTION =================
        $this->db->transStart();

        // insert ticket
        $this->ticket->insert([
            'ticket_code' => 'EVT-' . strtoupper(uniqid()),
            'event_id'    => $eventId,
            'user_id'     => session()->get('user_id'),
            'status'      => 'paid'
        ]);

        // kurangi quota
        $this->event->update($eventId, [
            'quota' => $event['quota'] - 1
        ]);

        $this->db->transComplete();
        // =================================================

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal membeli tiket');
        }

        return redirect()->to('/my-tickets')->with('success', 'Tiket berhasil dibeli');
    }
}
