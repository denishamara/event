<!DOCTYPE html>
<html>
<head>
    <title>Create Event - Admin</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        .content-centered {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        .phase-box {
            border: 2px dashed #e2e8f0;
            padding: 20px;
            border-radius: 14px;
            background: #f8fafc;
        }
    </style>
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">
<div class="content-centered">

<div class="card" style="max-width:900px;width:100%;">
    <h2>Create New Event</h2>
    <p style="color:#64748b;margin-bottom:24px;">Create event with dynamic pricing</p>

<form method="post" action="/admin/events/store" enctype="multipart/form-data">

<?= csrf_field() ?>

<div style="display:grid;gap:20px;">

<!-- EVENT TYPE SELECTOR -->
<div style="background:#f1f5f9;padding:16px;border-radius:8px;border:2px solid #e2e8f0;">
    <label style="font-weight:600;margin-bottom:12px;display:block;">Tipe Event</label>
    <div style="display:flex;gap:16px;">
        <label style="display:flex;align-items:center;cursor:pointer;">
            <input type="radio" name="event_type" value="regular" checked onchange="togglePricing(this.value)">
            <span style="margin-left:8px;">Event Reguler (Harga Tetap)</span>
        </label>
        <label style="display:flex;align-items:center;cursor:pointer;">
            <input type="radio" name="event_type" value="phase" onchange="togglePricing(this.value)">
            <span style="margin-left:8px;">Event dengan Pricing Phase</span>
        </label>
    </div>
</div>

<!-- BASIC -->
<label>Event Title</label>
<input type="text" name="title" required>

<label>Description</label>
<textarea name="description" required></textarea>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <div>
        <label>Location</label>
        <input type="text" name="location" required>
    </div>
    <div>
        <label>Date & Time</label>
        <input type="datetime-local" name="event_date" required>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <div>
        <label>Base Price</label>
        <input type="number" name="price" required>
    </div>
    <div>
        <label>Total Quota</label>
        <input type="number" name="quota" required>
    </div>
</div>

<label>Event Image</label>
<input type="file" name="image">

<hr>

<h3>Ticket Pricing (Optional)</h3>
<p style="color:#64748b;font-size:14px;">Each phase can use quota or time only</p>

<!-- EARLY -->
<div class="phase-box">
<h4>Early Bird</h4>

<label>Price</label>
<input type="number" name="price_early">

<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
    <div>
        <label style="font-size:12px;">Start (days before event)</label>
        <input type="number" name="early_start" placeholder="e.g. 30" min="1">
    </div>
    <div>
        <label style="font-size:12px;">End (days before event)</label>
        <input type="number" name="early_end" placeholder="e.g. 6" min="1">
    </div>
</div>
<p style="font-size:12px;color:#64748b;margin-top:5px;">💡 Recommended: H-30 to H-6 (no gaps with other phases)</p>

<label>
<input type="checkbox" name="early_use_quota" value="1">
 Use quota limit
</label>

<input type="number" name="early_quota" placeholder="Quota (optional)">
</div>

<!-- PRESALE -->
<div class="phase-box">
<h4>Presale</h4>

<label>Price</label>
<input type="number" name="price_presale">

<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
    <div>
        <label style="font-size:12px;">Start (days before event)</label>
        <input type="number" name="presale_start" placeholder="e.g. 5" min="1">
    </div>
    <div>
        <label style="font-size:12px;">End (days before event)</label>
        <input type="number" name="presale_end" placeholder="e.g. 3" min="1">
    </div>
</div>
<p style="font-size:12px;color:#64748b;margin-top:5px;">💡 Recommended: H-5 to H-3 (no gaps with other phases)</p>

<label>
<input type="checkbox" name="presale_use_quota" value="1">
 Use quota limit
</label>

<input type="number" name="presale_quota">
</div>

<!-- LAST -->
<div class="phase-box">
<h4>Last Minute</h4>

<label>Price</label>
<input type="number" name="price_last">

<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
    <div>
        <label style="font-size:12px;">Start (days before event)</label>
        <input type="number" name="last_start" placeholder="e.g. 2" min="1">
    </div>
    <div>
        <label style="font-size:12px;">End (days before event)</label>
        <input type="number" name="last_end" placeholder="e.g. 1" min="1">
    </div>
</div>
<p style="font-size:12px;color:#64748b;margin-top:5px;">💡 Recommended: H-2 to H-1 (no gaps with other phases)</p>

<label>
<input type="checkbox" name="last_use_quota" value="1">
 Use quota limit
</label>

<input type="number" name="last_quota">
</div>

<div style="display:flex;gap:12px;">
    <button class="btn" style="flex:1">Create Event</button>
    <a class="btn red" style="flex:1;text-align:center" href="/admin/events">Cancel</a>
</div>

</div>
</form>

<script>
function togglePricing(type) {
    const phaseBoxes = document.querySelectorAll('.phase-box');
    if (type === 'regular') {
        phaseBoxes.forEach(box => box.style.display = 'none');
    } else {
        phaseBoxes.forEach(box => box.style.display = 'block');
    }
}
// Hide phases by default (regular selected)
togglePricing('regular');
</script>

</div>

</div>
</div>

</body>
</html>
