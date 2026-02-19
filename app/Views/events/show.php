<!DOCTYPE html>
<html>
<head>
    <title>Event Detail - Event App</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">
<div class="content-centered">

<?php if (empty($event) || !isset($event['id'])): ?>

    <div class="card">
        <div style="text-align:center;padding:40px;color:#94a3b8;">
            <p style="font-size:18px;margin-bottom:8px;">❌ Event not found</p>
            <p style="font-size:14px;">The event you're looking for doesn't exist</p>
        </div>
    </div>

<?php else: ?>

<div class="event-card" style="max-width:900px;width:100%;margin:0 auto;">

    <!-- HEADER -->
    <div class="event-header">
        <h3 style="font-size:28px;margin-bottom:16px;">
            <?= esc($event['title']) ?>
        </h3>

        <div style="display:flex;gap:24px;flex-wrap:wrap;">
            <div class="event-info" style="color:#fff;">📍 <?= esc($event['location']) ?></div>
            <div class="event-info" style="color:#fff;">📅 <?= date('l, M d, Y', strtotime($event['event_date'])) ?></div>
            <div class="event-info" style="color:#fff;">⏰ <?= date('H:i', strtotime($event['event_date'])) ?> WIB</div>
        </div>
    </div>

    <!-- BODY -->
    <div class="event-body">

        <!-- PRICE & QUOTA -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:24px;">

            <div style="background:#f7fafc;padding:20px;border-radius:12px;text-align:center;">
                <div style="font-size:32px;font-weight:700;color:#667eea;">
                    Rp <?= number_format($event['display_price']) ?>
                </div>
                <div style="color:#64748b;font-size:14px;margin-top:8px;">
                    <?= esc($event['price_phase']) ?>
                </div>
            </div>

            <div style="background:#f7fafc;padding:20px;border-radius:12px;text-align:center;">
                <div style="font-size:32px;font-weight:700;color:#10b981;">
                    <?= esc($event['remaining_tickets']) ?>
                </div>
                <div style="color:#64748b;font-size:14px;margin-top:8px;">
                    🎫 Available Tickets
                </div>
            </div>

        </div>

        <!-- DESCRIPTION -->
        <div style="background:#f7fafc;padding:24px;border-radius:12px;margin-bottom:24px;">
            <h3>📝 About This Event</h3>
            <p style="white-space:pre-wrap;">
                <?= nl2br(esc($event['description'])) ?>
            </p>
        </div>

        <!-- PRICE RANGE TABLE -->
        <?php if (!empty($priceRanges)): ?>
        <div class="card">
            <h3>🎟 Ticket Price Range</h3>

            <table>
                <thead>
                    <tr>
                        <th>Phase</th>
                        <th>Price</th>
                        <th>Valid (H-)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($priceRanges as $p): ?>

                    <?php
                        // ✅ FIXED LOGIC
                        $isActive = ($activePriceId === $p['id']);
                    ?>

                    <tr style="<?= $isActive ? 'background:#ecfdf5;font-weight:600;' : '' ?>">
                        <td><?= esc($p['phase']) ?></td>
                        <td>Rp <?= number_format($p['price']) ?></td>
                        <td>H-<?= $p['start_day'] ?> → H-<?= $p['end_day'] ?></td>
                        <td><?= $isActive ? 'ACTIVE' : 'Inactive' ?></td>
                    </tr>

                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>

    <!-- FOOTER -->
    <div class="event-footer" style="display:flex;justify-content:flex-end;">
        <?php if (session()->get('role') === 'user'): ?>
            <a class="btn green" href="/checkout/<?= $event['id'] ?>">
                Buy Ticket Now
            </a>
        <?php endif; ?>
    </div>

</div>

<?php endif; ?>

</div>
</div>

</body>
</html>
