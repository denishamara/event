<?php
$mysqli = new mysqli('localhost', 'root', '', 'event_ticketing_ci4');
if ($mysqli->connect_errno) {
    echo 'DB CONNECT ERROR: ' . $mysqli->connect_error . PHP_EOL;
    exit(1);
}
$id = 34;
$res = $mysqli->query("SELECT * FROM payments WHERE id={$id}");
$p = $res ? $res->fetch_assoc() : null;
echo "PAYMENT:\n";
var_export($p);
echo PHP_EOL . PHP_EOL;
if ($p) {
    $tres = $mysqli->query('SELECT * FROM event_tickets WHERE id=' . (int)$p['event_ticket_id']);
    $t = $tres ? $tres->fetch_assoc() : null;
    echo "TICKET:\n";
    var_export($t);
    echo PHP_EOL . PHP_EOL;
    if ($t) {
        $ures = $mysqli->query('SELECT * FROM users WHERE id=' . (int)$t['user_id']);
        $u = $ures ? $ures->fetch_assoc() : null;
        echo "USER:\n";
        var_export($u);
        echo PHP_EOL . PHP_EOL;
    }
}
$mysqli->close();
