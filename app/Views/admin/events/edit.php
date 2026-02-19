<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
    <link rel="stylesheet" href="/assets/css/app.css">

    <style>
        .phase-box {
            border: 2px dashed #e2e8f0;
            padding: 20px;
            border-radius: 14px;
            background: #f8fafc;
            margin-bottom: 18px;
        }

        .event-image {
            margin-bottom: 20px;
        }

        .event-image img {
            max-width: 260px;
            border-radius: 14px;
            border: 2px solid #e2e8f0;
            display: block;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">

<div class="card" style="max-width:900px;margin:auto;">

    <h2>Edit Event</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:#fee;border:1px solid #f88;padding:12px;border-radius:8px;margin-bottom:20px;color:#c33;">
            ⚠️ <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:#efe;border:1px solid #8f8;padding:12px;border-radius:8px;margin-bottom:20px;color:#393;">
            ✅ <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <form method="post"
          action="/admin/events/update/<?= $event['id'] ?>"
          enctype="multipart/form-data"
          id="editEventForm">
        
        <?= csrf_field() ?>

        <!-- EVENT TYPE SELECTOR -->
        <?php 
        // Cek apakah event punya pricing phase dengan data valid
        $hasPhases = (!empty($priceMap['early']['price']) && $priceMap['early']['price'] !== '') || 
                     (!empty($priceMap['presale']['price']) && $priceMap['presale']['price'] !== '') || 
                     (!empty($priceMap['last']['price']) && $priceMap['last']['price'] !== '');
        $isRegular = !$hasPhases;
        ?>
        <div style="background:#f1f5f9;padding:16px;border-radius:8px;border:2px solid #e2e8f0;margin-bottom:20px;">
            <label style="font-weight:600;margin-bottom:12px;display:block;">Tipe Event</label>
            <div style="display:flex;gap:16px;">
                <label style="display:flex;align-items:center;cursor:pointer;">
                    <input type="radio" name="event_type" value="regular" <?= $isRegular ? 'checked' : '' ?> onchange="togglePricingSection(this.value)">
                    <span style="margin-left:8px;">Event Reguler (Harga Tetap)</span>
                </label>
                <label style="display:flex;align-items:center;cursor:pointer;">
                    <input type="radio" name="event_type" value="phase" <?= $hasPhases ? 'checked' : '' ?> onchange="togglePricingSection(this.value)">
                    <span style="margin-left:8px;">Event dengan Pricing Phase</span>
                </label>
            </div>
        </div>

        <!-- IMAGE -->
        <div class="event-image">
            <label>Current Image</label><br>

            <?php if (!empty($event['image'])): ?>
                <img src="/uploads/events/<?= esc($event['image']) ?>">
            <?php else: ?>
                <p style="color:#94a3b8;">No image uploaded</p>
            <?php endif; ?>

            <label>Change Image (optional)</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <hr>

        <!-- BASIC -->
        <label>Event Title</label>
        <input type="text" name="title"
               value="<?= esc($event['title']) ?>" required>

        <label>Description</label>
        <textarea name="description" required><?= esc($event['description']) ?></textarea>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div>
                <label>Location</label>
                <input type="text" name="location"
                       value="<?= esc($event['location']) ?>" required>
            </div>

            <div>
                <label>Event Date & Time</label>
                <input type="datetime-local" name="event_date"
                       value="<?= date('Y-m-d\TH:i', strtotime($event['event_date'])) ?>"
                       required>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div>
                <label>Regular Price (IDR)</label>
                <input type="number" name="price"
                       value="<?= esc($event['price']) ?>" required>
            </div>

            <div>
                <label>Total Quota Tiket</label>
                <input type="number" name="quota" id="totalQuota"
                       value="<?= esc($event['quota']) ?>" required onchange="calculateLastQuota()">
                <small style="color:#64748b;font-size:12px;">Total semua tiket yang tersedia</small>
            </div>
        </div>

        <hr>

        <!-- PHASES - HANYA TAMPIL JIKA PILIH "PHASE" -->
        <div id="pricing-phases-section" style="<?= $isRegular ? 'display:none;' : '' ?>">
        <h3>Ticket Pricing Phase</h3>
        <p style="color:#64748b;font-size:14px;margin-bottom:16px;">
            ⚠️ Total quota Early Bird + Presale harus ≤ Total Quota Event
        </p>

        <!-- EARLY -->
        <div class="phase-box">
            <h4>Early Bird</h4>
            <label>Harga Early Bird (IDR)</label>
            <input type="number" name="price_early"
                   value="<?= esc($priceMap['early']['price'] ?? '') ?>" required>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label>Start Day (hari sebelum event)</label>
                    <input type="number" name="early_start"
                           value="<?= esc($priceMap['early']['start_day'] ?? '') ?>" required>
                </div>
                <div>
                    <label>End Day (hari sebelum event)</label>
                    <input type="number" name="early_end"
                           value="<?= esc($priceMap['early']['end_day'] ?? '') ?>" required>
                </div>
            </div>

            <label>Quota Early Bird</label>
            <input type="number" name="early_quota" id="earlyQuota"
                   value="<?= esc($priceMap['early']['quota_total'] ?? '') ?>" onchange="calculateLastQuota()" required>
        </div>

        <!-- PRESALE -->
        <div class="phase-box">
            <h4>Presale</h4>
            <label>Harga Presale (IDR)</label>
            <input type="number" name="price_presale"
                   value="<?= esc($priceMap['presale']['price'] ?? '') ?>" required>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label>Start Day (hari sebelum event)</label>
                    <input type="number" name="presale_start"
                           value="<?= esc($priceMap['presale']['start_day'] ?? '') ?>" required>
                </div>
                <div>
                    <label>End Day (hari sebelum event)</label>
                    <input type="number" name="presale_end"
                           value="<?= esc($priceMap['presale']['end_day'] ?? '') ?>" required>
                </div>
            </div>

            <label>Quota Presale</label>
            <input type="number" name="presale_quota" id="presaleQuota"
                   value="<?= esc($priceMap['presale']['quota_total'] ?? '') ?>" onchange="calculateLastQuota()" required>
        </div>

        <!-- LAST -->
        <div class="phase-box">
            <h4>Last Minute</h4>
            <label>Harga Last Minute (IDR)</label>
            <input type="number" name="price_last"
                   value="<?= esc($priceMap['last']['price'] ?? '') ?>" required>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label>Start Day (hari sebelum event)</label>
                    <input type="number" name="last_start"
                           value="<?= esc($priceMap['last']['start_day'] ?? '') ?>" required>
                </div>
                <div>
                    <label>End Day (hari sebelum event)</label>
                    <input type="number" name="last_end"
                           value="<?= esc($priceMap['last']['end_day'] ?? '') ?>" required>
                </div>
            </div>

            <div style="background:#fef3c7;border:1px solid #f59e0b;padding:12px;border-radius:8px;margin-top:8px;">
                <p style="font-size:13px;color:#92400e;margin:0;">
                    ℹ️ Kuota Last Minute: <strong id="lastQuotaDisplay">Auto</strong> (otomatis dari sisa total kuota)
                </p>
            </div>
        </div>
        </div>

        <script>
        function calculateLastQuota() {
            const totalEl = document.getElementById('totalQuota');
            const earlyEl = document.getElementById('earlyQuota');
            const presaleEl = document.getElementById('presaleQuota');
            const displayEl = document.getElementById('lastQuotaDisplay');
            
            if (totalEl && earlyEl && presaleEl && displayEl) {
                const total = parseInt(totalEl.value) || 0;
                const early = parseInt(earlyEl.value) || 0;
                const presale = parseInt(presaleEl.value) || 0;
                const last = total - (early + presale);
                
                displayEl.textContent = last >= 0 ? last : '❌ Invalid (quota melebihi total)';
                displayEl.style.color = last >= 0 ? '#92400e' : '#dc2626';
            }
        }
        
        function togglePricingSection(type) {
            const phasesSection = document.getElementById('pricing-phases-section');
            
            if (phasesSection) {
                if (type === 'regular') {
                    phasesSection.style.display = 'none';
                    // Remove semua input phase dari DOM submit dengan set name kosong
                    const phaseInputs = phasesSection.querySelectorAll('input, textarea, select');
                    phaseInputs.forEach(input => {
                        input.removeAttribute('required');
                        input.disabled = true;
                    });
                } else {
                    phasesSection.style.display = 'block';
                    const phaseInputs = phasesSection.querySelectorAll('input, textarea, select');
                    phaseInputs.forEach(input => {
                        input.disabled = false;
                        input.setAttribute('required', 'required');
                    });
                }
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateLastQuota();
            
            // Set initial state based on radio button
            const checkedRadio = document.querySelector('input[name="event_type"]:checked');
            if (checkedRadio) {
                togglePricingSection(checkedRadio.value);
            }
        });
        </script>

        <div style="display:flex;gap:12px;margin-top:24px;">
            <button type="submit" class="btn" style="flex:1;cursor:pointer;">Update Event</button>
            <a class="btn red" style="flex:1;text-align:center;text-decoration:none;" href="/admin/events">Cancel</a>
        </div>

    </form>

</div>
</div>

</body>
</html>
