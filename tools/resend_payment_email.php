<?php
require __DIR__ . '/..\vendor\autoload.php';

// Bootstrap CodeIgniter environment
chdir(__DIR__ . '/..');
$_SERVER['CI_ENVIRONMENT'] = 'development';

use Config\Services;
use App\Models\PaymentModel;
use App\Models\EventTicketModel;
use App\Models\EventModel;
use App\Models\UserModel;

// Minimal bootstrap for CI services
require __DIR__ . '/..\spark';

$paymentId = 34;
$paymentModel = new PaymentModel();
$ticketModel  = new EventTicketModel();
$eventModel   = new EventModel();
$userModel    = new UserModel();

$payment = $paymentModel->find($paymentId);
if (!$payment) {
    echo "Payment not found: {$paymentId}\n";
    exit(1);
}

$firstTicket = $ticketModel->find($payment['event_ticket_id']);
if (!$firstTicket) {
    echo "Referenced ticket not found: {$payment['event_ticket_id']}\n";
    exit(1);
}

$event = $eventModel->find($firstTicket['event_id']);
$user  = $userModel->find($firstTicket['user_id']);

if (!$user || empty($user['email'])) {
    echo "User or email not found for ticket user_id={$firstTicket['user_id']}\n";
    exit(1);
}

$ticketsToSend = $ticketModel->where('event_id', $firstTicket['event_id'])
    ->where('user_id', $firstTicket['user_id'])
    ->where('status', 'paid')
    ->where('created_at', $firstTicket['created_at'])
    ->findAll();

if (empty($ticketsToSend)) {
    echo "No paid tickets found to resend for payment {$paymentId}\n";
    exit(1);
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
$email->setSubject('Resend: Your Tickets for ' . ($event['title'] ?? 'Event'));
$email->setMessage($ticketHtml);
$email->setMailType('html');

if ($email->send()) {
    echo "Email sent successfully to " . $user['email'] . "\n";
} else {
    echo "Email failed to send.\n";
    echo $email->printDebugger(['headers', 'subject', 'body']);
}
