<!DOCTYPE html>
<html>
<head>
    <title>Events - Event App</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">
    <h2>Explore Events</h2>
    <p style="color:#64748b;font-size:16px;margin-bottom:20px;">
        Discover and book events you’ll love
    </p>

    <div class="event-grid">

    <?php foreach ($events as $e): ?>

        <div class="event-card" style="position:relative;">

            <!-- ❤️ FAVORITE -->
            <?php if (session()->get('isLogin')): ?>
                <a href="/events/favorite/<?= $e['id'] ?>"
                   style="position:absolute;top:12px;left:12px;
                          font-size:20px;z-index:20;text-decoration:none;">
                    <?= $e['is_favorite'] ? '❤️' : '🤍' ?>
                </a>
            <?php endif; ?>

            <!-- LABEL -->
            <?php if ($e['is_expired']): ?>
                <div style="
                    position:absolute;
                    top:12px;
                    right:12px;
                    background:#64748b;
                    color:#fff;
                    padding:6px 12px;
                    border-radius:999px;
                    font-size:12px;
                    font-weight:700;
                    z-index:10;">
                    EVENT ENDED
                </div>

            <?php elseif ($e['remaining_tickets'] <= 0): ?>
                <div style="
                    position:absolute;
                    top:12px;
                    right:12px;
                    background:#ef4444;
                    color:#fff;
                    padding:6px 12px;
                    border-radius:999px;
                    font-size:12px;
                    font-weight:700;
                    z-index:10;">
                    SOLD OUT
                </div>

            <?php elseif ($e['is_almost_soldout']): ?>
                <div style="
                    position:absolute;
                    top:12px;
                    right:12px;
                    background:#f97316;
                    color:#fff;
                    padding:6px 12px;
                    border-radius:999px;
                    font-size:12px;
                    font-weight:700;
                    z-index:10;">
                    🔥 ALMOST SOLD OUT
                </div>

            <?php elseif ($e['price_phase'] !== 'Regular'): ?>
                <div style="
                    position:absolute;
                    top:12px;
                    right:12px;
                    background:#10b981;
                    color:#fff;
                    padding:6px 12px;
                    border-radius:999px;
                    font-size:12px;
                    font-weight:700;
                    z-index:10;">
                    <?= strtoupper(esc($e['price_phase'])) ?>
                </div>
            <?php endif; ?>

            <!-- IMAGE (SELALU ADA HEIGHT) -->
            <div style="
                width:100%;
                height:180px;
                background:#e2e8f0;
                border-radius:12px 12px 0 0;
                overflow:hidden;
                display:flex;
                align-items:center;
                justify-content:center;
                color:#64748b;
                font-size:14px;
            ">
                <?php if (!empty($e['image'])): ?>
                    <img src="/uploads/events/<?= esc($e['image']) ?>"
                         style="width:100%;height:100%;object-fit:cover;">
                <?php else: ?>
                    No Image
                <?php endif; ?>
            </div>

            <!-- HEADER -->
            <div class="event-header">
                <h3><?= esc($e['title']) ?></h3>

                <div class="event-info" style="color:#ffffff;">
                    📍 <?= esc($e['location']) ?>
                </div>

                <div class="event-info" style="color:#ffffff;">
                    📅 <?= date('M d, Y - H:i', strtotime($e['event_date'])) ?>
                </div>
            </div>

            <!-- BODY -->
            <div class="event-body">
                <div style="margin-bottom:10px;">
                    <span style="font-size:24px;font-weight:700;color:#667eea;">
                        Rp <?= number_format($e['display_price']) ?>
                    </span>

                    <small style="display:block;color:#64748b;">
                        <?= esc($e['price_phase']) ?>
                    </small>

                    <span style="display:block;color:#1e293b;font-size:22px;font-weight:700;margin-top:8px;">
                        <?= $e['remaining_tickets'] ?> <span style="font-size:14px;font-weight:500;color:#64748b;">tickets available</span>
                    </span>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="event-footer">

                <?php if ($e['is_expired']): ?>
                    <div class="btn"
                         style="background:#cbd5e1;color:#475569;cursor:not-allowed;width:100%;">
                        Event Ended
                    </div>

                <?php elseif ($e['remaining_tickets'] <= 0): ?>
                    <div class="btn red"
                         style="cursor:not-allowed;width:100%;">
                        Sold Out
                    </div>

                <?php else: ?>
                    <a class="btn"
                       href="/events/<?= $e['id'] ?>"
                       style="width:100%;text-align:center;">
                        View Details
                    </a>
                <?php endif; ?>

            </div>

        </div>

    <?php endforeach; ?>

    </div>
</div>

</body>
</html>

