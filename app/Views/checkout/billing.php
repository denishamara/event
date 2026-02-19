<!DOCTYPE html>
<html>
<head>
    <title>Billing - Konfirmasi Pembayaran</title>
    <link rel="stylesheet" href="/assets/css/app.css">

    <style>
        body {
            background:#f8fafc;
        }

        .billing-container {
            max-width:860px;
            margin:0 auto;
        }

        .billing-card {
            background:#ffffff;
            border-radius:20px;
            border:2px solid #e2e8f0;
            padding:32px;
        }

        .billing-header {
            text-align:center;
            padding-bottom:24px;
            border-bottom:2px dashed #e2e8f0;
            margin-bottom:24px;
        }

        .billing-header h2 {
            margin:0 0 8px 0;
            color:#1e293b;
        }

        .billing-number {
            font-family:monospace;
            font-size:18px;
            color:#6366f1;
            font-weight:700;
        }

        .detail-section {
            margin:24px 0;
        }

        .detail-section h3 {
            font-size:16px;
            color:#475569;
            margin:0 0 16px 0;
            padding-bottom:8px;
            border-bottom:1px solid #e2e8f0;
        }

        .detail-row {
            display:flex;
            justify-content:space-between;
            padding:12px 0;
            border-bottom:1px solid #f1f5f9;
        }

        .detail-label {
            color:#64748b;
            font-size:14px;
        }

        .detail-value {
            font-weight:600;
            color:#1e293b;
            text-align:right;
        }

        .total-section {
            background:linear-gradient(135deg, #eef2ff 0%, #e0f2fe 100%);
            padding:20px;
            border-radius:12px;
            margin:24px 0;
        }

        .total-row {
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .total-label {
            font-size:18px;
            font-weight:600;
            color:#475569;
        }

        .total-amount {
            font-size:28px;
            font-weight:700;
            color:#6366f1;
        }

        .payment-instruction {
            background:#fef3c7;
            border-left:4px solid #f59e0b;
            padding:16px;
            border-radius:8px;
            margin:24px 0;
        }

        .payment-instruction h4 {
            margin:0 0 8px 0;
            color:#92400e;
            font-size:14px;
        }

        .payment-instruction p {
            margin:4px 0;
            font-size:13px;
            color:#78350f;
        }

        .qr-section {
            text-align:center;
            margin:24px 0;
            padding:24px;
            background:#f8fafc;
            border-radius:12px;
        }

        .qr-section img {
            width:280px;
            height:280px;
            border-radius:12px;
            border:2px solid #e2e8f0;
            cursor:pointer;
            transition:transform 0.2s;
        }

        .qr-section img:hover {
            transform:scale(1.05);
        }

        .upload-section {
            border:2px dashed #cbd5e1;
            border-radius:12px;
            padding:24px;
            text-align:center;
            background:#f8fafc;
        }

        .upload-section input[type="file"] {
            margin:12px 0;
        }

        .btn-submit {
            width:100%;
            padding:18px;
            border-radius:14px;
            border:none;
            background:#10b981;
            color:#fff;
            font-weight:700;
            font-size:16px;
            cursor:pointer;
            margin-top:20px;
            transition:all 0.2s;
        }

        .btn-submit:hover {
            background:#059669;
            transform:translateY(-2px);
            box-shadow:0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-back {
            display:block;
            text-align:center;
            padding:12px;
            color:#64748b;
            text-decoration:none;
            margin-top:16px;
        }

        .btn-back:hover {
            color:#475569;
        }
    </style>
</head>

<body>

<?= view('partials/sidebar') ?>

<div class="content">
<div class="billing-container">

<div class="billing-card">

    <!-- HEADER -->
    <div class="billing-header">
        <h2>🧾 Invoice Pembayaran</h2>
        <div class="billing-number"><?= $billingNumber ?></div>
        <p style="color:#64748b;margin:8px 0 0 0;">
            <?= date('l, d F Y H:i', strtotime($orderData['created_at'])) ?>
        </p>
    </div>

    <!-- EVENT DETAIL -->
    <div class="detail-section">
        <h3>📋 Detail Event</h3>
        <div class="detail-row">
            <span class="detail-label">Event</span>
            <span class="detail-value"><?= esc($orderData['event_title']) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Lokasi</span>
            <span class="detail-value"><?= esc($orderData['event_location']) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Tanggal Event</span>
            <span class="detail-value"><?= date('d M Y H:i', strtotime($orderData['event_date'])) ?></span>
        </div>
    </div>

    <!-- ORDER DETAIL -->
    <div class="detail-section">
        <h3>🎫 Detail Pesanan</h3>
        <div class="detail-row">
            <span class="detail-label">Phase Tiket</span>
            <span class="detail-value"><?= esc($orderData['phase']) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Jumlah Tiket</span>
            <span class="detail-value"><?= $orderData['qty'] ?> tiket</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Harga per Tiket</span>
            <span class="detail-value">Rp <?= number_format($orderData['price_per_ticket']) ?></span>
        </div>
    </div>

    <!-- TOTAL -->
    <div class="total-section">
        <div class="total-row">
            <span class="total-label">Total Pembayaran</span>
            <span class="total-amount">Rp <?= number_format($orderData['total']) ?></span>
        </div>
    </div>

    <!-- PAYMENT INSTRUCTION -->
    <div class="payment-instruction">
        <h4>⚠️ Instruksi Pembayaran</h4>
        <p>1. Lakukan pembayaran melalui <b><?= strtoupper($orderData['payment_method']) ?></b></p>
        <p>2. Scan QR Code di bawah atau transfer ke nomor yang tertera</p>
        <p>3. Setelah pembayaran selesai, upload bukti pembayaran</p>
        <p>4. Tunggu admin untuk memverifikasi pembayaran Anda</p>
    </div>

    <!-- QR CODE -->
    <div class="qr-section">
        <p style="font-weight:600;margin:0 0 12px 0;">Scan QR Code untuk Pembayaran</p>
        <a href="<?= $qrUrl ?>" target="_blank">
            <img src="<?= $qrUrl ?>" alt="QR Payment">
        </a>
        <p style="color:#64748b;font-size:13px;margin:12px 0 0 0;">
            Klik untuk memperbesar
        </p>
    </div>

    <!-- UPLOAD BUKTI -->
    <form method="post" action="/checkout/submit-payment" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="order_id" value="<?= $orderData['order_id'] ?>">

        <div class="upload-section">
            <h4 style="margin:0 0 8px 0;color:#475569;">📤 Upload Bukti Pembayaran</h4>
            <p style="color:#64748b;font-size:13px;margin:0 0 12px 0;">
                Format: JPG, PNG, atau PDF (Max 5MB)
            </p>
            <input type="file" name="payment_proof" accept="image/*,application/pdf" required>
        </div>

        <button type="submit" class="btn-submit">
            ✅ Konfirmasi Pembayaran
        </button>
    </form>

    <a href="/checkout/<?= $orderData['event_id'] ?>" class="btn-back">
        ← Kembali ke Checkout
    </a>

</div>

</div>
</div>

</body>
</html>
