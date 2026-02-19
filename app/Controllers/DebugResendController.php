<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\EventTicketModel;
use App\Models\EventModel;
use App\Models\UserModel;
use Config\Services;

class DebugResendController extends BaseController
{
    public function resend($paymentId)
    {
        $paymentModel = new PaymentModel();
        $ticketModel  = new EventTicketModel();
        $eventModel   = new EventModel();
        $userModel    = new UserModel();

        $payment = $paymentModel->find($paymentId);
        if (!$payment) {
            return "Payment not found: {$paymentId}";
        }

        if (empty($payment['event_ticket_id'])) {
            return "Payment missing ticket reference";
        }

        $firstTicket = $ticketModel->find($payment['event_ticket_id']);
        if (!$firstTicket) {
            return "Referenced ticket not found: {$payment['event_ticket_id']}";
        }

        $event = $eventModel->find($firstTicket['event_id']);
        $user  = $userModel->find($firstTicket['user_id']);

        if (!$user || empty($user['email'])) {
            return "User or email not found for ticket user_id={$firstTicket['user_id']}";
        }

        $ticketsToSend = $ticketModel->where('event_id', $firstTicket['event_id'])
            ->where('user_id', $firstTicket['user_id'])
            ->where('status', 'paid')
            ->where('created_at', $firstTicket['created_at'])
            ->findAll();

        if (empty($ticketsToSend)) {
            return "No paid tickets found to resend for payment {$paymentId}";
        }

        $ticketHtml = view('tickets/email_ticket', [
            'tickets' => $ticketsToSend,
            'event'   => $event,
            'user'    => $user,
            'qty'     => $payment['qty'] ?? count($ticketsToSend)
        ]);

        $email = Services::email();
        $email->setFrom('denishamara07@gmail.com', 'CI4 Event App');
        $email->setTo($user['email']);
        $email->setSubject('Debug Resend: Your Tickets for ' . ($event['title'] ?? 'Event'));
        $email->setMessage($ticketHtml);
        $email->setMailType('html');

        if ($email->send()) {
            return "Email sent successfully to " . $user['email'];
        }

        return "Email failed to send. Debug info:\n" . $email->printDebugger(['headers', 'subject', 'body']);
    }
}
