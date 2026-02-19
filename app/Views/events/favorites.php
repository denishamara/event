<!DOCTYPE html>
<html>
<head>
    <title>My Favorite Events</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">

<h2>❤️ My Favorite Events</h2>
<p style="color:#64748b;margin-bottom:20px;">
    Events you’ve saved for later
</p>

<?php if (empty($events)): ?>
    <div class="card" style="text-align:center;padding:40px;color:#94a3b8;">
        You haven’t added any favorites yet.
    </div>
<?php else: ?>

<div class="event-grid">

<?php foreach ($events as $e): ?>

    <div class="event-card" style="position:relative;">

        <!-- ❤️ FAVORITE -->
        <a href="/events/favorite/<?= $e['id'] ?>"
           style="position:absolute;top:12px;left:12px;
                  font-size:20px;z-index:20;text-decoration:none;">
            ❤️
        </a>

        <!-- LABEL -->
        <?php if ($e['is_expired']): ?>
            <div style="position:absolute;top:12px;right:12px;background:#64748b;color:#fff;
                padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;">
                EVENT ENDED
            </div>

        <?php elseif ($e['remaining_tickets'] <= 0): ?>
            <div style="position:absolute;top:12px;right:12px;background:#ef4444;color:#fff;
                padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;">
                SOLD OUT
            </div>

        <?php elseif ($e['is_almost_soldout']): ?>
            <div style="position:absolute;top:12px;right:12px;background:#f97316;color:#fff;
                padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;">
                🔥 ALMOST SOLD OUT
            </div>
        <?php endif; ?>

        <!-- IMAGE -->
        <div style="
            width:100%;
            height:180px;
            background:#e2e8f0;
            border-radius:12px 12px 0 0;
            overflow:hidden;
            display:flex;
            align-items:center;
            justify-content:center;
            color:#64748b;">
            <?php if (!empty($e['image'])): ?>
                <img src="/uploads/events/<?= esc($e['image']) ?>"
                     style="width:100%;height:100%;object-fit:cover;">
            <?php else: ?>
                No Image
            <?php endif; ?>
        </div>

        <div class="event-header">
            <h3><?= esc($e['title']) ?></h3>
            <div class="event-info" style="color:#fff;">
                📍 <?= esc($e['location']) ?>
            </div>
            <div class="event-info" style="color:#fff;">
                📅 <?= date('M d, Y - H:i', strtotime($e['event_date'])) ?>
            </div>
        </div>

        <div class="event-body">
            <span style="font-size:24px;font-weight:700;color:#667eea;">
                Rp <?= number_format($e['display_price']) ?>
            </span>

            <small style="display:block;color:#64748b;">
                <?= esc($e['price_phase']) ?>
            </small>

            <span style="display:block;color:#64748b;font-size:14px;margin-top:4px;">
                <?= $e['remaining_tickets'] ?> tickets available
            </span>
        </div>

        <div class="event-footer">
            <?php if ($e['remaining_tickets'] <= 0 || $e['is_expired']): ?>
                <div class="btn red" style="cursor:not-allowed;">Unavailable</div>
            <?php else: ?>
                <a href="/events/<?= $e['id'] ?>" class="btn">View Details</a>
            <?php endif; ?>
        </div>

    </div>

<?php endforeach; ?>

</div>
<?php endif; ?>

</div>
</body>
</html>
