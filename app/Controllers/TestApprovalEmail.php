<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\EventTicketModel;
use App\Models\UserModel;
use Config\Services;

class TestApprovalEmail extends BaseController
{
    public function index()
    {
        // Test email with real data from last pending payment
        $ticket = new EventTicketModel();
        $event = new EventModel();
        $user = new UserModel();

        $testTicket = $ticket->where('status', 'paid')->first();
        if (!$testTicket) {
            return 'No paid tickets found to test';
        }

        $eventData = $event->find($testTicket['event_id']);
        $userData = $user->find($testTicket['user_id']);

        $tickets = $ticket->where('user_id', $testTicket['user_id'])
            ->where('event_id', $testTicket['event_id'])
            ->where('status', 'paid')
            ->findAll();

        $ticketHtml = view('tickets/email_ticket', [
            'tickets' => $tickets,
            'event'   => $eventData,
            'user'    => $userData,
            'qty'     => count($tickets)
        ]);

        $email = Services::email();
        $email->setFrom('denishamara07@gmail.com', 'CI4 Event App');
        $email->setTo($userData['email']);
        $email->setSubject('TEST - Payment Approved - ' . $eventData['title']);
        $email->setMessage($ticketHtml);
        $email->setMailType('html');

        if ($email->send()) {
            echo "✅ Email sent successfully to " . $userData['email'];
        } else {
            echo "❌ Email failed<br><br>";
            echo $email->printDebugger(['headers', 'subject', 'body']);
        }
    }
}
