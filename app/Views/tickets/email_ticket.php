<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial;
            background: #f8fafc;
            padding: 20px;
        }
        .container {
            max-width: 650px;
            margin: auto;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .ticket {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            border: 2px dashed #2563eb;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 16px;
            text-align: center;
        }
        .info {
            margin-bottom: 10px;
            font-size: 14px;
        }
        .qr {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .qr img {
            border-radius: 8px;
        }
        .qr p {
            margin-top: 12px;
            color: #64748b;
            font-size: 13px;
        }
        .footer {
            margin-top: 24px;
            font-size: 12px;
            color: #64748b;
            text-align: center;
            padding: 16px;
            background: #f8fafc;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<?php 
// Support both old (tickets array) and new (single ticket) format
$isGroupTicket = isset($ticket);
$displayQty = $qty ?? 1;
?>

<div class="container">
    <div class="header">
        <h1>🎟️ Your Event Ticket<?= $displayQty > 1 ? 's' : '' ?></h1>
        <p>Thank you for your purchase! <?= $displayQty > 1 ? 'Here are your ' . $displayQty . ' tickets' : 'Here is your ticket' ?></p>
    </div>

    <?php if ($isGroupTicket): ?>
    <!-- GRUP TICKET (1 ticket dengan qty) -->
    <div class="ticket">
        <div class="title">TICKET #<?= $ticket['id'] ?></div>

        <div class="info"><b>Ticket Code:</b> <?= $ticket['ticket_code'] ?></div>
        <div class="info"><b>Event:</b> <?= $event['title'] ?></div>
        <div class="info"><b>Location:</b> <?= $event['location'] ?></div>
        <div class="info"><b>Date:</b> <?= date('l, F d, Y', strtotime($event['event_date'])) ?></div>
        <div class="info"><b>Attendee:</b> <?= $user['name'] ?></div>
        <?php if ($displayQty > 1): ?>
        <div class="info" style="color:#10b981;font-weight:bold;"><b>Quantity:</b> 📦 <?= $displayQty ?> Tickets</div>
        <div class="info"><b>Price per ticket:</b> Rp <?= number_format($ticket['price']) ?></div>
        <div class="info" style="color:#667eea;font-weight:bold;"><b>Total:</b> Rp <?= number_format($ticket['price'] * $displayQty) ?></div>
        <?php else: ?>
        <div class="info"><b>Price:</b> Rp <?= number_format($ticket['price']) ?></div>
        <?php endif; ?>
        <?php if (isset($ticket['phase']) && $ticket['phase'] !== 'Regular'): ?>
        <div class="info"><b>Phase:</b> <?= $ticket['phase'] ?></div>
        <?php endif; ?>

        <div class="qr">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($ticket['qr_data'] ?? $ticket['ticket_code']) ?>" alt="QR Code">
            <p>Scan this QR Code at the venue<?= $displayQty > 1 ? ' (valid for ' . $displayQty . ' people)' : '' ?></p>
        </div>
    </div>
    <?php else: ?>
    <!-- OLD FORMAT: Multiple individual tickets (fallback) -->
    <?php foreach ($tickets as $t): ?>
    <div class="ticket">
        <div class="title">TICKET #<?= $t['id'] ?></div>

        <div class="info"><b>Ticket Code:</b> <?= $t['ticket_code'] ?></div>
        <div class="info"><b>Event:</b> <?= $event['title'] ?></div>
        <div class="info"><b>Location:</b> <?= $event['location'] ?></div>
        <div class="info"><b>Date:</b> <?= date('l, F d, Y', strtotime($event['event_date'])) ?></div>
        <div class="info"><b>Attendee:</b> <?= $user['name'] ?></div>
        <?php if (isset($t['price'])): ?>
        <div class="info"><b>Price:</b> Rp <?= number_format($t['price']) ?></div>
        <?php endif; ?>
        <?php if (isset($t['phase'])): ?>
        <div class="info"><b>Phase:</b> <?= $t['phase'] ?></div>
        <?php endif; ?>

        <div class="qr">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= $t['ticket_code'] ?>" alt="QR Code">
            <p>Scan this QR Code at the venue</p>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <div class="footer">
        <strong>Important:</strong> Please bring these tickets (digital or printed) to enter the event.<br>
        Keep your ticket codes safe and do not share them with others.
    </div>
</div>

</body>
</html>
