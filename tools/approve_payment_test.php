<?php
$mysqli = new mysqli('localhost', 'root', '', 'event_ticketing_ci4');
if ($mysqli->connect_errno) {
    echo 'DB CONNECT ERROR: ' . $mysqli->connect_error . PHP_EOL; exit(1);
}
$paymentId = 34;
$res = $mysqli->query("SELECT * FROM payments WHERE id={$paymentId}");
$payment = $res? $res->fetch_assoc():null;
if (!$payment) { echo "Payment not found: {$paymentId}\n"; exit(1); }

$update = $mysqli->query("UPDATE payments SET status='paid' WHERE id={$paymentId}");
if ($update) { echo "Payment {$paymentId} status set to paid\n"; } else { echo "Failed to update payment: " . $mysqli->error . "\n"; }

// Find referenced ticket
$ticketId = (int)$payment['event_ticket_id'];
$tres = $mysqli->query("SELECT * FROM event_tickets WHERE id={$ticketId}");
$ticket = $tres? $tres->fetch_assoc():null;
if (!$ticket) { echo "Referenced ticket not found: {$ticketId}\n"; exit(1); }

$eventId = (int)$ticket['event_id'];
$userId  = (int)$ticket['user_id'];
$createdAt = $mysqli->real_escape_string($ticket['created_at']);

$updateTickets = $mysqli->query("UPDATE event_tickets SET status='paid' WHERE event_id={$eventId} AND user_id={$userId} AND created_at='{$createdAt}'");
if ($updateTickets) {
    echo "Tickets updated to paid for user={$userId}, event={$eventId}\n";
    echo "Affected rows: " . $mysqli->affected_rows . "\n";
} else {
    echo "Failed to update tickets: " . $mysqli->error . "\n";
}

$mysqli->close();
