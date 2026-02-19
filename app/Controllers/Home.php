<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // Redirect if already logged in
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        $db = \Config\Database::connect();

        // Get upcoming events (limit 6)
        $upcomingEvents = $db->table('events')
            ->where('event_date >=', date('Y-m-d'))
            ->orderBy('event_date', 'ASC')
            ->limit(6)
            ->get()
            ->getResultArray();

        // Get statistics
        $totalEvents = $db->table('events')->countAll();
        $totalUsers = $db->table('users')->where('role', 'user')->countAll();
        $totalTicketsSold = $db->table('event_tickets')->where('status', 'paid')->countAll();

        return view('landing_page', [
            'upcomingEvents' => $upcomingEvents,
            'totalEvents' => $totalEvents,
            'totalUsers' => $totalUsers,
            'totalTicketsSold' => $totalTicketsSold
        ]);
    }
}
