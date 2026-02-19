<?php

namespace App\Controllers;

use App\Models\EventTicketModel;
use App\Models\EventModel;

class MyTicketController extends BaseController
{
    protected $ticket;
    protected $event;

    public function __construct()
    {
        $this->ticket = new EventTicketModel();
        $this->event  = new EventModel();
    }

    /* ===============================
       LIST MY TICKETS
    =============================== */
    public function index()
    {
        if (!session()->get('isLogin')) {
            return redirect()->to('/login');
        }

        $tickets = $this->ticket
            ->select('
                event_tickets.*,
                events.title   AS event_title,
                events.location AS event_location,
                events.event_date
            ')
            ->join('events', 'events.id = event_tickets.event_id')
            ->where('event_tickets.user_id', session()->get('user_id'))
            ->orderBy('event_tickets.created_at', 'DESC')
            ->findAll();

        return view('tickets/my', [
            'tickets' => $tickets
        ]);
    }

    /* ===============================
       VIEW SINGLE TICKET
    =============================== */
    public function view($id)
    {
        if (!session()->get('isLogin')) {
            return redirect()->to('/login');
        }

        $ticket = $this->ticket
            ->where('id', $id)
            ->where('user_id', session()->get('user_id'))
            ->first();

        if (!$ticket) {
            return redirect()->to('/my-tickets');
        }

        // Only allow viewing active (paid) tickets
        if ($ticket['status'] !== 'paid') {
            return redirect()->to('/my-tickets')->with('error', 'Ticket not active');
        }

        $event = $this->event->find($ticket['event_id']);

        return view('tickets/view', [
            'ticket' => $ticket,
            'event'  => $event
        ]);
    }
}
