<!DOCTYPE html>
<html>
<head>
    <title>My Tickets - Event App</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        .tickets-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 32px;
            border-radius: 16px;
            margin-bottom: 24px;
        }
        .ticket-card {
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 16px;
        }
        .ticket-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
        }
        .ticket-code {
            font-family: monospace;
            background: #eef2ff;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 8px;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }
        .badge.paid { background:#dcfce7;color:#166534; }
        .badge.pending { background:#fef9c3;color:#92400e; }
        .badge.refund_requested { background:#fee2e2;color:#991b1b; }
        .badge.refunded { background:#e0f2fe;color:#075985; }
        .badge.rejected { background:#f1f5f9;color:#475569; }
        .badge.cancelled { background:#ffffff; color:#0f172a; border:1px solid #e2e8f0; }
        .ticket-footer {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .ticket-actions { display:flex; align-items:center; gap:8px }
        .ticket-actions a.btn { margin-top:12px }
    </style>
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">

    <div class="tickets-header">
        <h2>🎫 My Tickets</h2>
        <p>View and manage your tickets</p>
    </div>

    <?php if (empty($tickets)): ?>
        <div class="card" style="text-align:center;padding:40px">
            <h3>No tickets yet</h3>
            <a class="btn" href="/events">Browse Events</a>
        </div>
    <?php else: ?>
        <?php foreach ($tickets as $t): ?>
            <div class="ticket-card">

                <div class="ticket-header">
                    <div>
                        <h3><?= esc($t['event_title']) ?></h3>
                        <div class="ticket-code">🎟 <?= esc($t['ticket_code']) ?></div>
                        <?php if (isset($t['qty']) && $t['qty'] > 1): ?>
                        <div style="background:#10b981;color:white;padding:6px 12px;border-radius:8px;display:inline-block;margin-top:8px;font-weight:700;font-size:14px">
                            📦 <?= $t['qty'] ?> Tiket Grup
                        </div>
                        <?php endif; ?>

                        <p>📍 <?= esc($t['event_location']) ?></p>
                        <p>📅 <?= date('d M Y H:i', strtotime($t['event_date'])) ?></p>
                    </div>

                    <div>
                        <span class="badge <?= esc($t['status']) ?>">
                            <?= strtoupper(str_replace('_',' ', $t['status'])) ?>
                        </span>
                    </div>
                </div>

                <div class="ticket-footer">

                    <!-- STATUS MESSAGE -->
                    <?php if ($t['status'] === 'paid'): ?>
                        <span style="color:#16a34a;font-weight:600">✅ Ticket active</span>
                    <?php elseif ($t['status'] === 'pending'): ?>
                        <span style="color:#ca8a04;font-weight:600">⏳ Waiting admin approval</span>
                    <?php elseif ($t['status'] === 'refund_requested'): ?>
                        <span style="color:#dc2626;font-weight:600">💸 Refund requested</span>
                    <?php elseif ($t['status'] === 'refunded'): ?>
                        <span style="color:#0284c7;font-weight:600">↩️ Refunded</span>
                    <?php else: ?>
                        <span style="color:#64748b;font-weight:600">❌ Not active</span>
                    <?php endif; ?>

                    <!-- ACTION -->
                    <div class="ticket-actions">
                        <?php if ($t['status'] === 'paid'): ?>
                            <?php 
                            // Cek apakah masih bisa refund (minimal H-2, artinya > 2 hari)
                            $eventDate = new DateTime($t['event_date']);
                            $today = new DateTime();
                            $daysDiff = $today->diff($eventDate)->days;
                            $isBeforeEvent = $today < $eventDate;
                            $canRefund = $isBeforeEvent && $daysDiff > 2;
                            ?>
                            
                            <a class="btn" href="/tickets/view/<?= $t['id'] ?>">View</a>
                            
                            <?php if ($canRefund): ?>
                                <button type="button" class="btn" id="refund-toggle-<?= $t['id'] ?>">Request Refund</button>
                            <?php else: ?>
                                <button type="button" class="btn" disabled style="opacity:0.5;cursor:not-allowed;" title="Refund hanya bisa dilakukan minimal H-2 sebelum event">
                                    Refund Closed
                                </button>
                            <?php endif; ?>
                        <?php elseif ($t['status'] === 'pending'): ?>
                            <span style="font-size:13px;color:#64748b">Please wait</span>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- REFUND FORM (COLLAPSIBLE) -->
                <?php if ($t['status'] === 'paid'): ?>
                <?php 
                // Cek lagi untuk form (minimal H-2, artinya > 2 hari)
                $eventDate = new DateTime($t['event_date']);
                $today = new DateTime();
                $daysDiff = $today->diff($eventDate)->days;
                $isBeforeEvent = $today < $eventDate;
                $canRefund = $isBeforeEvent && $daysDiff > 2;
                ?>
                <?php if ($canRefund): ?>
                <div id="refund-form-<?= $t['id'] ?>" style="display:none;margin-top:16px;padding-top:16px;border-top:2px solid #e2e8f0">
                    <form method="post" action="/refund/request">
                        <?= csrf_field() ?>
                        <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">

                        <label style="display:block;margin-bottom:8px;font-weight:600;color:#334155">Reason for refund</label>
                        <textarea name="refund_reason" placeholder="Please explain why you want a refund..." required
                            style="width:100%;min-height:100px;padding:12px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical"></textarea>

                        <div style="margin-top:12px;display:flex;gap:8px">
                            <button type="submit" class="btn red">Submit Refund Request</button>
                            <button type="button" class="btn" onclick="toggleRefund(<?= $t['id'] ?>)">Cancel</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
</body>
<script>
function toggleRefund(id){
    var el = document.getElementById('refund-form-' + id);
    if (!el) return;
    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
}

document.addEventListener('click', function(e){
    if (!e.target) return;
    var id = e.target.id;
    if (id && id.indexOf('refund-toggle-') === 0){
        var tid = id.replace('refund-toggle-','');
        toggleRefund(tid);
    }
});
</script>
</html>
