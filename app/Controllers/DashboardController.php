<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\EventTicketModel;
use App\Models\ChatModel;

class DashboardController extends BaseController
{
    protected $event;
    protected $ticket;
    protected $chat;

    public function __construct()
    {
        $this->event  = new EventModel();
        $this->ticket = new EventTicketModel();
        $this->chat   = new ChatModel();
    }

    public function index()
    {
        if (!session()->get('isLogin')) {
            return redirect()->to('/login');
        }

        $role   = session()->get('role');
        $userId = session()->get('user_id');

        // DATA DEFAULT (BIAR AMAN DI VIEW)
        $data = [
            'events'      => [],
            'myTickets'   => [],
            'totalEvents' => 0,
            'totalTicketsSold' => 0,
            'totalChats'  => 0,
            'chats'       => []
        ];

        // ================= USER =================
        if ($role === 'user') {
            $data['events'] = $this->event
                ->where('event_date >=', date('Y-m-d H:i:s'))
                ->orderBy('event_date', 'DESC')
                ->findAll();

            $data['myTickets'] = $this->ticket
                ->select('event_tickets.*, events.title as event_title')
                ->join('events', 'events.id = event_tickets.event_id', 'left')
                ->where('event_tickets.user_id', $userId)
                ->orderBy('event_tickets.created_at', 'DESC')
                ->findAll();
        }

        // ================= ADMIN =================
        if ($role === 'admin') {
            $data['totalEvents'] = $this->event->countAllResults();

            $data['totalTicketsSold'] = $this->ticket
                ->where('status', 'paid')
                ->countAllResults();

            $data['totalChats'] = $this->chat->countAllResults();
        }

        // ================= AGENT =================
        if ($role === 'agent') {
            $data['chats'] = $this->chat
                ->orderBy('created_at', 'DESC')
                ->findAll();
        }

        // ✅ SATU-SATUNYA VIEW DASHBOARD
        return view('dashboard/index', $data);
    }
}
