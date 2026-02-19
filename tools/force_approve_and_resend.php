<?php
// Usage: php tools/force_approve_and_resend.php <payment_id>
$paymentId = isset($argv[1]) ? (int)$argv[1] : 0;
if (!$paymentId) { echo "Usage: php tools/force_approve_and_resend.php <payment_id>\n"; exit(1); }
$host = '127.0.0.1'; $user = 'root'; $pass = ''; $db = 'event_ticketing_ci4'; $port = 3306;
$mysqli = new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_errno) { echo "DB_CONNECT_ERR:" . $mysqli->connect_error . "\n"; exit(1); }
$paymentRes = $mysqli->query("SELECT * FROM payments WHERE id={$paymentId}");
if (!$paymentRes || $paymentRes->num_rows === 0) { echo "PAYMENT_NOT_FOUND\n"; exit(1); }
$payment = $paymentRes->fetch_assoc();
// Find first ticket
$firstTicket = null;
if (!empty($payment['event_ticket_id'])) {
    $tres = $mysqli->query('SELECT * FROM event_tickets WHERE id=' . (int)$payment['event_ticket_id']);
    if ($tres && $tres->num_rows) $firstTicket = $tres->fetch_assoc();
}
if (!$firstTicket) { echo "FIRST_TICKET_NOT_FOUND\n"; exit(1); }
$eventId = (int)$firstTicket['event_id'];
$userId = (int)$firstTicket['user_id'];
$createdAt = $mysqli->real_escape_string($firstTicket['created_at']);
$qty = isset($payment['qty']) && is_numeric($payment['qty']) ? (int)$payment['qty'] : 1;

// Start transaction
$mysqli->begin_transaction();
$ok = true;
// 1) update payment to paid
if (!$mysqli->query("UPDATE payments SET status='paid' WHERE id={$paymentId}")) { echo "PAYMENT_UPDATE_ERR:" . $mysqli->error . "\n"; $ok=false; }
// 2) update tickets matching event/user/created_at and status '' or 'pending' limit qty
if ($ok) {
    $updateTicketsSql = "UPDATE event_tickets SET status='paid' WHERE event_id={$eventId} AND user_id={$userId} AND created_at='{$createdAt}' AND (status='' OR status='pending') LIMIT {$qty}";
    if (!$mysqli->query($updateTicketsSql)) { echo "TICKET_UPDATE_ERR:" . $mysqli->error . "\n"; $ok=false; }
}
// 3) reduce event quota
if ($ok) {
    $er = $mysqli->query("SELECT quota FROM events WHERE id={$eventId}");
    if ($er && $er->num_rows) {
        $ev = $er->fetch_assoc();
        $newQuota = max(0, ((int)$ev['quota']) - $qty);
        if (!$mysqli->query("UPDATE events SET quota={$newQuota} WHERE id={$eventId}")) { echo "QUOTA_UPDATE_ERR:" . $mysqli->error . "\n"; $ok=false; }
    } else { echo "EVENT_NOT_FOUND\n"; $ok=false; }
}

if ($ok) $mysqli->commit(); else $mysqli->rollback();

if (!$ok) { echo "Transaction failed\n"; exit(1); }

// 4) call debug resend endpoint
$url = "http://localhost:8080/debug/resend/{$paymentId}";
$options = [
    'http' => [
        'method' => 'GET',
        'timeout' => 10
    ]
];
$context = stream_context_create($options);
$result = @file_get_contents($url, false, $context);
if ($result === false) {
    echo "RESEND_CALL_FAILED. Tried: {$url}\n";
    // Try alternative localhost with /event if needed
    $alt = "http://localhost/event/debug/resend/{$paymentId}";
    $result2 = @file_get_contents($alt, false, $context);
    if ($result2 === false) {
        echo "RESEND_CALL_FAILED_ALT. Tried: {$alt}\n";
    } else {
        echo "RESEND_ALT_RESPONSE:\n" . $result2 . "\n";
    }
} else {
    echo "RESEND_RESPONSE:\n" . $result . "\n";
}
$mysqli->close();
