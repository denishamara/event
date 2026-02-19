<!DOCTYPE html>
<html>
<head>
    <title>Checkout Ticket</title>
    <link rel="stylesheet" href="/assets/css/app.css">

    <style>
        body {
            background:#f8fafc;
        }

        .checkout-container {
            max-width:860px;
            margin:0 auto;
        }

        .checkout-card {
            background:#ffffff;
            border-radius:20px;
            border:2px solid #e2e8f0;
            padding:32px;
        }

        /* HEADER */
        .event-title {
            font-size:24px;
            font-weight:700;
            margin-bottom:6px;
        }

        .event-meta {
            display:flex;
            gap:18px;
            font-size:14px;
            color:#64748b;
            flex-wrap:wrap;
        }

        hr {
            border:none;
            border-top:1px solid #e2e8f0;
            margin:26px 0;
        }

        /* PHASE */
        .phase-row {
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:12px 14px;
            border-radius:12px;
            background:#f8fafc;
            border:1px solid #e2e8f0;
            margin-bottom:10px;
        }
        
        .phase-row.active {
            background: linear-gradient(135deg, #dcfce7 0%, #d1fae5 100%);
            border: 2px solid #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        
        .phase-row.inactive {
            background: #f1f5f9;
            opacity: 0.7;
        }
        
        .phase-row.inactive .phase-name,
        .phase-row.inactive .badge {
            color: #64748b !important;
        }

        .phase-name {
            font-weight:600;
            color: #1e293b;
        }

        .badge {
            font-size:12px;
            padding:4px 10px;
            border-radius:999px;
            font-weight:600;
        }
        
        .badge.active-now {
            background: #10b981;
            color: #ffffff;
            font-weight: 700;
        }

        .early { background:#dcfce7;color:#166534; }
        .presale { background:#e0f2fe;color:#075985; }
        .last { background:#fee2e2;color:#991b1b; }
        
        .badge.inactive-badge {
            background: #e2e8f0;
            color: #64748b;
        }

        /* SUMMARY */
        .summary-row {
            display:flex;
            justify-content:space-between;
            margin:10px 0;
            font-size:15px;
        }

        .summary-total {
            border-top:1px dashed #cbd5e1;
            margin-top:14px;
            padding-top:14px;
            font-size:18px;
            font-weight:700;
        }

        /* PAYMENT */
        .payment-grid {
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:14px;
        }

        .payment-item {
            border:2px solid #e2e8f0;
            border-radius:14px;
            padding:14px;
            text-align:center;
            cursor:pointer;
            font-weight:600;
        }

        .payment-item.active {
            border-color:#6366f1;
            background:#eef2ff;
            transform: scale(1.05);
        }

        .warning {
            font-size:13px;
            color:#f59e0b;
            margin-top:10px;
        }
        
        .info-box {
            background: #e0f2fe;
            border-left: 4px solid #0284c7;
            padding: 16px;
            border-radius: 8px;
            margin: 16px 0;
        }
        
        .info-box.warning {
            background: #fef3c7;
            border-left-color: #f59e0b;
        }
        
        .info-box.error {
            background: #fee2e2;
            border-left-color: #ef4444;
        }
        
        .info-box.success {
            background: #dcfce7;
            border-left-color: #10b981;
        }

        button {
            width:100%;
            padding:16px;
            border-radius:14px;
            border:none;
            background:#10b981;
            color:#fff;
            font-weight:700;
            margin-top:18px;
        }

        button:disabled {
            background:#cbd5e0;
        }
    </style>
</head>

<body>

<?= view('partials/sidebar') ?>

<div class="content">
<div class="checkout-container">

<div class="checkout-card">

    <!-- EVENT INFO -->
    <div class="event-title"><?= esc($event['title']) ?></div>

    <div class="event-meta">
        <div>📍 <?= esc($event['location']) ?></div>
        <div>📅 <?= date('l, d F Y H:i', strtotime($event['event_date'])) ?></div>
        <?php if (isset($event['days_until_event'])): ?>
        <div>⏰ <?= $event['days_until_event'] ?> hari lagi</div>
        <?php endif; ?>
    </div>

    <hr>

    <!-- PHASE INFO -->
    <h3>🎫 Ticket Phase</h3>
    
    <?php if (isset($event['active_phase_has_quota']) && !$event['active_phase_has_quota']): ?>
    <div class="info-box error">
        <strong>❌ Tidak Ada Phase Aktif</strong>
        <p style="margin:8px 0 0 0;">Saat ini tidak ada phase tiket yang aktif atau semua kuota phase aktif sudah habis. 
        <?php if (isset($event['next_phase'])): ?>
            Phase berikutnya: <strong><?= esc($event['next_phase']['phase']) ?></strong> akan aktif pada <strong><?= $event['next_phase']['start_day'] ?> hari sebelum event</strong>.
        <?php else: ?>
            Silakan hubungi admin untuk informasi lebih lanjut.
        <?php endif; ?>
        </p>
    </div>
    <?php else: ?>
    <div class="info-box success">
        <strong>✅ Phase Aktif: <?= esc($event['price_phase']) ?></strong>
        <p style="margin:8px 0 0 0;">Anda dapat membeli tiket pada phase ini dengan harga <strong>Rp <?= number_format($event['display_price']) ?></strong></p>
    </div>
    <?php endif; ?>

    <?php foreach ($pricePhases as $p): ?>
        <div class="phase-row <?= isset($p['is_active']) && $p['is_active'] ? 'active' : 'inactive' ?>">
            <div>
                <span class="phase-name"><?= esc($p['phase']) ?></span>
                <span class="badge <?= isset($p['is_active']) && $p['is_active'] ? 'active-now' : 'inactive-badge' ?>">
                    <?php if (isset($p['is_active']) && $p['is_active']): ?>
                        ✓ AKTIF SEKARANG
                    <?php else: ?>
                        <?= $p['start_day'] ?>-<?= $p['end_day'] ?> hari sebelum event
                    <?php endif; ?>
                </span>
            </div>
            <div style="<?= isset($p['is_active']) && $p['is_active'] ? '' : 'color:#64748b;' ?>">
                Rp <?= number_format($p['price']) ?>
                <?php if (isset($p['is_active']) && $p['is_active']): ?>
                    · Sisa <?= (int)$p['quota_remaining'] ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <p class="warning">
        ⚠ Anda hanya dapat membeli tiket dari phase yang sedang aktif.
    </p>

    <hr>

    <!-- FORM -->
    <form method="post" action="/checkout/process" enctype="multipart/form-data" <?= (isset($event['active_phase_has_quota']) && !$event['active_phase_has_quota']) ? 'style="pointer-events:none;opacity:0.5;"' : '' ?>>
        <?= csrf_field() ?>

        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
        <input type="hidden" name="qty" id="qtyHidden" value="1">
        <input type="hidden" name="payment_method" id="paymentMethod">

        <label><b>Jumlah Tiket</b></label>
        <input type="number" id="qty" min="1" value="1"
               style="width:100%;margin-top:8px;" 
               <?= (isset($event['active_phase_has_quota']) && !$event['active_phase_has_quota']) ? 'disabled' : '' ?>>

        <hr>

        <!-- SUMMARY -->
        <h3>🧾 Ringkasan Pembelian</h3>
        <div id="summaryBox"></div>

        <div class="summary-total">
            Total Pembayaran:
            Rp <span id="totalPrice">0</span>
        </div>

        <hr>

        <!-- PAYMENT -->
        <h3>💳 Metode Pembayaran</h3>

        <div class="payment-grid">
            <div class="payment-item" onclick="selectPayment('dana',this)">
                <div style="font-size:24px;margin-bottom:4px;">💳</div>
                <div>DANA</div>
            </div>
            <div class="payment-item" onclick="selectPayment('ovo',this)">
                <div style="font-size:24px;margin-bottom:4px;">💰</div>
                <div>OVO</div>
            </div>
            <div class="payment-item" onclick="selectPayment('shopeepay',this)">
                <div style="font-size:24px;margin-bottom:4px;">🛒</div>
                <div>ShopeePay</div>
            </div>
            <div class="payment-item" onclick="selectPayment('brimo',this)">
                <div style="font-size:24px;margin-bottom:4px;">🏦</div>
                <div>BRIMO</div>
            </div>
        </div>

        <button id="checkoutBtn" type="submit" disabled>
            🛒 Lanjut ke Billing
        </button>

    </form>

</div>

</div>
</div>

<script>
const phases = <?= json_encode($pricePhases) ?>;
const hasActivePhase = <?= json_encode(isset($event['active_phase_has_quota']) ? $event['active_phase_has_quota'] : true) ?>;

const qtyInput = document.getElementById('qty');
const qtyHidden = document.getElementById('qtyHidden');
const summaryBox = document.getElementById('summaryBox');
const totalEl = document.getElementById('totalPrice');
const checkoutBtn = document.getElementById('checkoutBtn');

let selectedMethod = '';

function calculate() {
    if (!hasActivePhase) {
        summaryBox.innerHTML = '<div style="color:#ef4444;font-weight:600;">Tidak ada phase aktif. Checkout tidak tersedia.</div>';
        totalEl.innerText = '0';
        return;
    }
    
    let qty = parseInt(qtyInput.value) || 1;
    qtyHidden.value = qty;

    // Cari phase yang aktif
    const activePhase = phases.find(p => p.is_active === true && p.quota_remaining > 0);
    
    if (!activePhase) {
        summaryBox.innerHTML = '<div style="color:#ef4444;font-weight:600;">Tidak ada phase aktif dengan kuota tersedia.</div>';
        totalEl.innerText = '0';
        return;
    }
    
    // Cek apakah qty melebihi kuota
    if (qty > activePhase.quota_remaining) {
        summaryBox.innerHTML = `<div style="color:#ef4444;font-weight:600;">❌ Jumlah melebihi kuota phase aktif! Kuota tersisa: ${activePhase.quota_remaining}</div>`;
        totalEl.innerText = '0';
        submitBtn.disabled = true;
        return;
    }
    
    let total = qty * activePhase.price;
    
    summaryBox.innerHTML = `
        <div class="summary-row">
            <div>
                <b>${activePhase.phase}</b> <span style="color:#10b981;font-size:12px;">● AKTIF</span><br>
                <small>${qty} × Rp ${activePhase.price.toLocaleString('id-ID')}</small>
            </div>
            <div>
                Rp ${total.toLocaleString('id-ID')}
            </div>
        </div>
    `;

    totalEl.innerText = total.toLocaleString('id-ID');
}

function selectPayment(method, el) {
    if (!hasActivePhase) return;
    
    document.querySelectorAll('.payment-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');

    selectedMethod = method;
    document.getElementById('paymentMethod').value = method;
    
    // Cek lagi apakah qty valid sebelum enable button
    const qty = parseInt(qtyInput.value) || 1;
    const activePhase = phases.find(p => p.is_active === true && p.quota_remaining > 0);
    
    if (activePhase && qty <= activePhase.quota_remaining) {
        checkoutBtn.disabled = false;
    }
}

qtyInput.addEventListener('input', calculate);
calculate();
</script>

</body>
</html>
