<?php
// Usage: php tools/inspect_payment.php <payment_id>
// Use local XAMPP defaults if CI classes aren't available
$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';
$dbName = 'event_ticketing_ci4';
$dbPort = 3306;
$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($mysqli->connect_errno) {
    echo "DB_CONNECT_ERR:" . $mysqli->connect_error . "\n";
    exit(1);
}
$pid = isset($argv[1]) ? (int)$argv[1] : 0;
if (!$pid) {
    echo "Usage: php tools/inspect_payment.php <payment_id>\n";
    exit(1);
}
$res = $mysqli->query("SELECT * FROM payments WHERE id={$pid}");
if (!$res || $res->num_rows === 0) {
    echo "PAYMENT_NOT_FOUND\n";
    exit(0);
}
$p = $res->fetch_assoc();
echo "PAYMENT:" . json_encode($p, JSON_PRETTY_PRINT) . "\n";
$firstTicket = null;
if (!empty($p['event_ticket_id'])) {
    $tres = $mysqli->query('SELECT * FROM event_tickets WHERE id=' . (int)$p['event_ticket_id']);
    if ($tres && $tres->num_rows) {
        $firstTicket = $tres->fetch_assoc();
        echo "FIRST_TICKET:" . json_encode($firstTicket, JSON_PRETTY_PRINT) . "\n";
    }
}
// Find tickets for same event & user
$eventId = $firstTicket['event_id'] ?? null;
$userId = $firstTicket['user_id'] ?? null;
if ($eventId && $userId) {
    $stmt = $mysqli->prepare('SELECT * FROM event_tickets WHERE event_id=? AND user_id=? ORDER BY created_at ASC');
    $stmt->bind_param('ii', $eventId, $userId);
    $stmt->execute();
    $r = $stmt->get_result();
    $arr = [];
    while ($row = $r->fetch_assoc()) $arr[] = $row;
    echo "ALL_TICKETS_FOR_USER_EVENT:" . json_encode($arr, JSON_PRETTY_PRINT) . "\n";

    // Count pending tickets
    $cres = $mysqli->prepare('SELECT COUNT(*) as cnt FROM event_tickets WHERE event_id=? AND user_id=? AND status="pending"');
    $cres->bind_param('ii', $eventId, $userId);
    $cres->execute();
    $cr = $cres->get_result()->fetch_assoc();
    echo "PENDING_COUNT:" . ($cr['cnt'] ?? 0) . "\n";
}
// Show payments for same user/event
if ($userId) {
    $pres = $mysqli->prepare('SELECT * FROM payments WHERE event_ticket_id IN (SELECT id FROM event_tickets WHERE user_id=? AND event_id=?)');
    $pres->bind_param('ii', $userId, $eventId);
    $pres->execute();
    $pr = $pres->get_result();
    $parr = [];
    while ($row = $pr->fetch_assoc()) $parr[] = $row;
    echo "RELATED_PAYMENTS:" . json_encode($parr, JSON_PRETTY_PRINT) . "\n";
}
$mysqli->close();
