<?php
// Setup database columns for simple rollover system
$conn = new mysqli('localhost', 'root', '', 'event_ticketing_ci4');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Setting up rollover columns...\n";

// Add columns if they don't exist
$columns = ['transferred_out', 'transferred_in', 'original_quota'];
foreach ($columns as $col) {
    $check = $conn->query("SHOW COLUMNS FROM event_prices LIKE '{$col}'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE event_prices ADD COLUMN {$col} INT DEFAULT 0");
        echo "Added column: {$col}\n";
    } else {
        echo "Column already exists: {$col}\n";
    }
}

// Set original_quota for existing data if not set
$conn->query("UPDATE event_prices SET original_quota = quota_total WHERE original_quota = 0 OR original_quota IS NULL");
echo "Set original_quota for existing records\n";

echo "\nSetup complete!\n";
$conn->close();
