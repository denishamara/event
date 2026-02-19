<!DOCTYPE html>
<html>
<head>
    <title>Pending Payments - Admin</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="/assets/js/custom-alert.js"></script>
    <style>
        .payment-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .payment-card:hover {
            border-color: #667eea;
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.15);
        }
        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f1f5f9;
        }
        .proof-image {
            max-width: 100%;
            max-height: 400px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            cursor: pointer;
        }
        .proof-image:hover {
            border-color: #667eea;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin: 16px 0;
        }
        .info-item {
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
        }
        .info-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 16px;
            color: #1a202c;
            font-weight: 600;
        }
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .btn-approve {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        }
        .btn-reject {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
        }
    </style>
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 32px; border-radius: 16px; margin-bottom: 24px;">
        <h2 style="color: white; margin-bottom: 8px;">⏳ Pending Payments</h2>
        <p style="color: rgba(255, 255, 255, 0.9);">Review and approve payment proofs</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px;">
            ✅ <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="background: #fee2e2; color: #dc2626; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px;">
            ❌ <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($payments)): ?>
        <div class="card">
            <div style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                <div style="font-size: 64px; margin-bottom: 16px; opacity: 0.5;">💳</div>
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #64748b;">No Pending Payments</div>
                <div style="font-size: 14px; color: #94a3b8;">All payments have been reviewed</div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($payments as $p): ?>
            <div class="payment-card">
                <div class="payment-header">
                    <div>
                        <h3 style="margin: 0 0 8px 0; color: #1a202c;">Payment #<?= $p['payment_id'] ?></h3>
                        <p style="margin: 0; color: #64748b; font-size: 14px;">
                            Submitted: <?= date('M d, Y H:i', strtotime($p['created_at'])) ?>
                        </p>
                    </div>
                    <span class="badge" style="background: #fef3c7; color: #a16207; font-size: 13px; padding: 8px 16px; border-radius: 20px;">
                        PENDING REVIEW
                    </span>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Customer</div>
                        <div class="info-value"><?= esc($p['user_name']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Event</div>
                        <div class="info-value"><?= esc($p['event_title']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Quantity</div>
                        <div class="info-value"><?= $p['qty'] ?> ticket(s)</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Amount</div>
                        <div class="info-value">Rp <?= number_format($p['amount']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Payment Method</div>
                        <div class="info-value"><?= strtoupper($p['method']) ?></div>
                    </div>
                </div>

                <?php if (!empty($p['proof_file'])): ?>
                    <div style="margin: 20px 0;">
                        <div style="font-weight: 600; margin-bottom: 12px; color: #475569;">📎 Payment Proof:</div>
                        <?php 
                        $filePath = WRITEPATH . 'uploads/proofs/' . $p['proof_file'];
                        $fileExt = pathinfo($p['proof_file'], PATHINFO_EXTENSION);
                        ?>
                        <?php if (in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png'])): ?>
                            <a href="/admin/payments/view-proof/<?= $p['payment_id'] ?>" target="_blank">
                                <img src="/admin/payments/view-proof/<?= $p['payment_id'] ?>" 
                                     alt="Payment Proof" 
                                     class="proof-image">
                            </a>
                        <?php else: ?>
                            <a href="/admin/payments/download-proof/<?= $p['payment_id'] ?>" 
                               class="btn" 
                               style="display: inline-block;">
                                📄 Download Proof (<?= strtoupper($fileExt) ?>)
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="action-buttons">
                    <form method="post" action="/admin/payments/approve/<?= $p['payment_id'] ?>" style="display: inline;" id="approveForm<?= $p['payment_id'] ?>">
                        <?= csrf_field() ?>
                        <button type="button" class="btn-approve" onclick="confirmApprove(<?= $p['payment_id'] ?>)">
                            ✅ Approve Payment
                        </button>
                    </form>
                    <form method="post" action="/admin/payments/reject/<?= $p['payment_id'] ?>" style="display: inline;" id="rejectForm<?= $p['payment_id'] ?>">
                        <?= csrf_field() ?>
                        <button type="button" class="btn-reject" onclick="confirmReject(<?= $p['payment_id'] ?>)">
                            ❌ Reject Payment
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function confirmApprove(paymentId) {
    customConfirm(
        'Are you sure you want to approve this payment?',
        'Approve Payment',
        function() {
            document.getElementById('approveForm' + paymentId).submit();
        },
        null,
        'Yes, Approve',
        'Cancel'
    );
}

function confirmReject(paymentId) {
    customConfirm(
        'Are you sure you want to reject this payment? This action cannot be undone.',
        'Reject Payment',
        function() {
            document.getElementById('rejectForm' + paymentId).submit();
        },
        null,
        'Yes, Reject',
        'Cancel'
    );
}
</script>

</body>
</html>
