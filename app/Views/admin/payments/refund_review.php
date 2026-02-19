<!DOCTYPE html>
<html>
<head>
    <title>Review Refund</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">

    <h2>💸 Review Refund</h2>

    <div class="card" style="max-width:700px">

        <p><b>User:</b> <?= esc($refund['user_name']) ?></p>
        <p><b>Email:</b> <?= esc($refund['user_email']) ?></p>
        <p><b>Event:</b> <?= esc($refund['event_title']) ?></p>
        <p><b>Ticket:</b> <?= esc($refund['ticket_code'] ?? '-') ?></p>
        <p><b>Requested At:</b> <?= esc($refund['created_at'] ?? '-') ?></p>
        <p><b>Status:</b> 
            <span style="font-weight:600;color:<?= $refund['status'] === 'approved' ? '#10b981' : ($refund['status'] === 'rejected' ? '#ef4444' : '#f59e0b') ?>">
                <?= strtoupper($refund['status']) ?>
            </span>
        </p>

        <hr>

        <p><b>Refund Reason:</b></p>
        <div style="background:#f8fafc;padding:12px;border-radius:8px">
            <?= nl2br(esc($refund['reason'] ?? '-')) ?>
        </div>

        <?php if ($refund['status'] === 'pending'): ?>
        <hr>

        <form method="post" action="/admin/refunds/approve/<?= $refund['id'] ?>">
            <?= csrf_field() ?>
            <button class="btn green">
                ✅ Approve Refund
            </button>
        </form>

        <form method="post"
              action="/admin/refunds/reject/<?= $refund['id'] ?>"
              style="margin-top:10px">
            <?= csrf_field() ?>
            <button class="btn red">
                ❌ Reject Refund
            </button>
        </form>
        <?php endif; ?>

    </div>

</div>

</body>
</html>
