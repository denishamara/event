<!DOCTYPE html>
<html>
<head>
    <title>Refund Requests</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        .badge { padding:6px 12px; border-radius:20px; font-weight:700; font-size:12px; display:inline-block }
        .badge.pending { background:#fef9c3; color:#92400e }
        .badge.refund_requested { background:#fee2e2; color:#991b1b }
        .badge.approved { background:#dcfce7; color:#166534 }
        .badge.rejected { background:#fee2f0; color:#9f1239 }
        .badge.refunded { background:#e0f2fe; color:#075985 }
    </style>
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">

    <h2>💸 Refund Requests</h2>
    <p style="color:#64748b;margin-bottom:20px">
        Review and process user refund requests
    </p>

    <?php if (empty($refunds)): ?>
        <div class="card" style="text-align:center;padding:40px">
            No refund requests found
        </div>
    <?php else: ?>
        <div class="card">
            <table width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Ticket</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($refunds as $r): ?>
                    <tr>
                        <td>#<?= $r['id'] ?></td>
                        <td>
                            <?= esc($r['user_name']) ?><br>
                            <small><?= esc($r['user_email']) ?></small>
                        </td>
                        <td><?= esc($r['event_title']) ?></td>
                        <td><?= esc($r['ticket_code'] ?? '-') ?></td>
                        <td>
                            <span class="badge <?= esc($r['status'] ?? 'pending') ?>">
                                <?= strtoupper($r['status'] ?? 'pending') ?>
                            </span>
                        </td>
                        <td>
                            <a class="btn" href="/admin/refunds/<?= $r['id'] ?>">
                                Review
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
