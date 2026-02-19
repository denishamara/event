<?php
// Koneksi database manual
$host = 'localhost';
$dbname = 'event_ticketing_ci4';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>DEBUG EVENT PHASES</h1>";
    echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#fff;} table{border-collapse:collapse;width:100%;margin:20px 0;background:#2d2d2d;} th,td{border:1px solid #444;padding:12px;text-align:left;} th{background:#0d7377;color:#fff;} .active{background:#1e5128;font-weight:bold;} .inactive{background:#3d0000;} h2{color:#0d7377;border-bottom:2px solid #0d7377;padding-bottom:10px;} .info{background:#1a1a2e;padding:15px;border-left:4px solid #0d7377;margin:10px 0;}</style>";
    
    // Ambil event "phase"
    $stmt = $pdo->query("SELECT * FROM events WHERE title = 'phase' ORDER BY id DESC LIMIT 1");
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        echo "<p>Event 'phase' tidak ditemukan!</p>";
        exit;
    }
    
    $eventDate = strtotime($event['event_date']);
    $today = time();
    $daysUntilEvent = ceil(($eventDate - $today) / 86400);
    
    echo "<h2>EVENT: {$event['title']} (ID: {$event['id']})</h2>";
    echo "<div class='info'>";
    echo "<strong>Event Date:</strong> {$event['event_date']}<br>";
    echo "<strong>Today:</strong> " . date('Y-m-d H:i:s') . "<br>";
    echo "<strong>Days Until Event:</strong> {$daysUntilEvent} hari<br>";
    echo "<strong>Base Price:</strong> Rp " . number_format($event['price']) . "<br>";
    echo "<strong>Quota:</strong> {$event['quota']}<br>";
    echo "</div>";
    
    // Ambil phases
    $stmt = $pdo->prepare("SELECT * FROM event_prices WHERE event_id = ? ORDER BY order_index ASC");
    $stmt->execute([$event['id']]);
    $prices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($prices)) {
        echo "<p style='color:#f00;'>⚠️ TIDAK ADA PHASES DIKONFIGURASI UNTUK EVENT INI!</p>";
    } else {
        echo "<h3>PHASES:</h3>";
        echo "<table>";
        echo "<tr><th>Order</th><th>Phase</th><th>Price</th><th>Start Day</th><th>End Day</th><th>Use Quota</th><th>Quota Total</th><th>Quota Remaining</th><th>Status</th></tr>";
        
        $activePhase = null;
        foreach ($prices as $p) {
            $isInRange = ($daysUntilEvent >= $p['start_day'] && $daysUntilEvent <= $p['end_day']);
            $hasQuota = !$p['use_quota'] || $p['quota_remaining'] > 0;
            $isActive = $isInRange && $hasQuota;
            
            if ($isActive && !$activePhase) {
                $activePhase = $p;
            }
            
            $statusClass = $isActive ? 'active' : 'inactive';
            $statusText = $isActive ? '✓ ACTIVE' : '✗ Inactive';
            
            if ($isInRange && !$hasQuota) {
                $statusText = '✗ In Range but No Quota';
            } elseif (!$isInRange) {
                $statusText = "✗ Out of Range (H-{$daysUntilEvent})";
            }
            
            echo "<tr class='{$statusClass}'>";
            echo "<td>{$p['order_index']}</td>";
            echo "<td><strong>{$p['phase']}</strong></td>";
            echo "<td>Rp " . number_format($p['price']) . "</td>";
            echo "<td>H-{$p['start_day']}</td>";
            echo "<td>H-{$p['end_day']}</td>";
            echo "<td>" . ($p['use_quota'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($p['use_quota'] ? $p['quota_total'] : 'N/A') . "</td>";
            echo "<td>" . ($p['use_quota'] ? $p['quota_remaining'] : 'N/A') . "</td>";
            echo "<td><strong>{$statusText}</strong></td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        if ($activePhase) {
            echo "<div class='info' style='border-left-color:#1e5128;'>";
            echo "<h3 style='color:#1e5128;'>✓ ACTIVE PHASE RESULT:</h3>";
            echo "<strong>Phase:</strong> {$activePhase['phase']}<br>";
            echo "<strong>Price:</strong> Rp " . number_format($activePhase['price']) . "<br>";
            echo "<strong>Range:</strong> H-{$activePhase['start_day']} to H-{$activePhase['end_day']}<br>";
            echo "</div>";
        } else {
            echo "<div class='info' style='border-left-color:#f00;'>";
            echo "<h3 style='color:#f00;'>⚠️ NO ACTIVE PHASE - USING REGULAR</h3>";
            echo "<strong>Price:</strong> Rp " . number_format($event['price']) . " (Base Price)<br>";
            echo "</div>";
        }
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
