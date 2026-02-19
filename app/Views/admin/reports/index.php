<!DOCTYPE html>
<html>
<head>
    <title>Event Sales Report</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <div>
        <h2>📊 Event Sales Report</h2>
        <p style="color:#64748b;">
            Sales summary based on remaining quota and paid tickets
        </p>
    </div>
    <a href="/admin/reports/export-pdf<?php 
        $params = [];
        if ($search) $params['q'] = $search;
        if ($transactionSearch ?? '') $params['t_search'] = $transactionSearch;
        if ($transactionEvent ?? '') $params['t_event'] = $transactionEvent;
        if ($transactionMethod ?? '') $params['t_method'] = $transactionMethod;
        if ($transactionStatus ?? '') $params['t_status'] = $transactionStatus;
        if ($transactionDateFrom ?? '') $params['t_date_from'] = $transactionDateFrom;
        if ($transactionDateTo ?? '') $params['t_date_to'] = $transactionDateTo;
        echo $params ? '?' . http_build_query($params) : '';
    ?>" 
       class="btn btn-primary"
       style="background:#ef4444;display:inline-flex;align-items:center;gap:8px;text-decoration:none;padding:12px 20px;border-radius:8px;color:white;font-weight:600;">
        <i class="fa-solid fa-file-pdf"></i>
        <span>Download PDF</span>
    </a>
</div>

<!-- SUMMARY -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:30px;">

    <div class="card">
        <small>Total Revenue</small>
        <h2 style="color:#10b981;">Rp <?= number_format($totalRevenue) ?></h2>
    </div>

    <div class="card">
        <small>Total Sold Tickets</small>
        <h2><?= $totalSold ?></h2>
    </div>

    <div class="card">
        <small>Total Remaining Tickets</small>
        <h2 style="color:#f97316;"><?= $totalRemaining ?></h2>
    </div>

</div>

<!-- SEARCH -->
<form method="get" style="margin-bottom:24px;">
    <input type="search"
           name="q"
           placeholder="Search event..."
           value="<?= esc($search) ?>"
           style="max-width:360px;">
</form>

<!-- EVENT TABLE -->
<div class="card" style="margin-bottom:30px;">
<h3>🎯 Event Performance</h3>

<table>
<thead>
<tr>
    <th>Event</th>
    <th>Date</th>
    <th>Sold</th>
    <th>Remaining</th>
    <th>Revenue</th>
    <th>Status</th>
</tr>
</thead>

<tbody>
<?php foreach ($events as $e): ?>
<tr>
    <td><strong><?= esc($e['title']) ?></strong></td>
    <td><?= date('d M Y', strtotime($e['event_date'])) ?></td>
    <td style="color:#10b981;font-weight:600;"><?= $e['sold'] ?></td>
    <td style="color:#f97316;font-weight:600;"><?= $e['remaining'] ?></td>
    <td>Rp <?= number_format($e['revenue']) ?></td>
    <td>
        <?= $e['ended']
            ? '<span style="color:#ef4444;font-weight:600;">ENDED</span>'
            : '<span style="color:#10b981;font-weight:600;">ONGOING</span>' ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<!-- TRANSACTIONS -->
<div class="card">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <h3>📋 Transaction Detail</h3>
</div>

<!-- TRANSACTION FILTERS -->
<form method="get" style="background:#f8fafc;padding:20px;border-radius:8px;margin-bottom:20px;">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;">
        
        <!-- Search -->
        <div>
            <label style="display:block;font-size:12px;color:#64748b;margin-bottom:5px;">Search</label>
            <input type="text" 
                   name="t_search" 
                   placeholder="Ticket/Event..." 
                   value="<?= esc($transactionSearch ?? '') ?>"
                   style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px;">
        </div>

        <!-- Event -->
        <div>
            <label style="display:block;font-size:12px;color:#64748b;margin-bottom:5px;">Event</label>
            <select name="t_event" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px;">
                <option value="">All Events</option>
                <?php foreach($allEvents as $evt): ?>
                    <option value="<?= $evt['id'] ?>" <?= ($transactionEvent ?? '') == $evt['id'] ? 'selected' : '' ?>>
                        <?= esc($evt['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Method -->
        <div>
            <label style="display:block;font-size:12px;color:#64748b;margin-bottom:5px;">Payment Method</label>
            <select name="t_method" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px;">
                <option value="">All Methods</option>
                <option value="dana" <?= ($transactionMethod ?? '') == 'dana' ? 'selected' : '' ?>>DANA</option>
                <option value="shopeepay" <?= ($transactionMethod ?? '') == 'shopeepay' ? 'selected' : '' ?>>SHOPEEPAY</option>
                <option value="ovo" <?= ($transactionMethod ?? '') == 'ovo' ? 'selected' : '' ?>>OVO</option>
                <option value="brimo" <?= ($transactionMethod ?? '') == 'brimo' ? 'selected' : '' ?>>BRIMO</option>
            </select>
        </div>

        <!-- Status -->
        <div>
            <label style="display:block;font-size:12px;color:#64748b;margin-bottom:5px;">Status</label>
            <select name="t_status" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px;">
                <option value="">All Status</option>
                <option value="pending" <?= ($transactionStatus ?? '') == 'pending' ? 'selected' : '' ?>>PENDING</option>
                <option value="paid" <?= ($transactionStatus ?? '') == 'paid' ? 'selected' : '' ?>>PAID</option>
                <option value="rejected" <?= ($transactionStatus ?? '') == 'rejected' ? 'selected' : '' ?>>REJECTED</option>
            </select>
        </div>

        <!-- Date From -->
        <div>
            <label style="display:block;font-size:12px;color:#64748b;margin-bottom:5px;">From Date</label>
            <input type="date" 
                   name="t_date_from" 
                   value="<?= esc($transactionDateFrom ?? '') ?>"
                   style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px;">
        </div>

        <!-- Date To -->
        <div>
            <label style="display:block;font-size:12px;color:#64748b;margin-bottom:5px;">To Date</label>
            <input type="date" 
                   name="t_date_to" 
                   value="<?= esc($transactionDateTo ?? '') ?>"
                   style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px;">
        </div>

    </div>

    <!-- Preserve event search filter -->
    <?php if ($search): ?>
        <input type="hidden" name="q" value="<?= esc($search) ?>">
    <?php endif; ?>

    <div style="margin-top:15px;display:flex;gap:10px;">
        <button type="submit" 
                style="background:#667eea;color:white;border:none;padding:10px 20px;border-radius:6px;font-weight:600;cursor:pointer;">
            <i class="fa-solid fa-filter"></i> Apply Filter
        </button>
        <a href="/admin/reports<?= $search ? '?q=' . urlencode($search) : '' ?>" 
           style="background:#94a3b8;color:white;border:none;padding:10px 20px;border-radius:6px;font-weight:600;text-decoration:none;display:inline-block;">
            <i class="fa-solid fa-rotate-left"></i> Reset
        </a>
    </div>
</form>

<table>
<thead>
<tr>
    <th>Ticket</th>
    <th>Event</th>
    <th>Type</th>
    <th>Method</th>
    <th>Amount</th>
    <th>Status</th>
    <th>Date</th>
</tr>
</thead>

<tbody id="transactionTableBody">
<?php foreach ($transactions as $t): ?>
<tr>
    <td><?= esc($t['ticket_code']) ?></td>
    <td><?= esc($t['event_title']) ?></td>
    <td>
        <?php if ($t['type'] === 'refund'): ?>
            <span style="color:#ef4444;font-weight:700;">REFUND</span>
        <?php else: ?>
            <span style="color:#10b981;font-weight:700;">PAYMENT</span>
        <?php endif; ?>
    </td>
    <td>
        <?php if ($t['type'] === 'refund'): ?>
            <span style="color:#94a3b8;">-</span>
        <?php else: ?>
            <?= strtoupper($t['method']) ?>
        <?php endif; ?>
    </td>
    <td>
        <?php if ($t['type'] === 'refund'): ?>
            <span style="color:#ef4444;">- Rp <?= number_format($t['amount']) ?></span>
        <?php else: ?>
            <span style="color:#10b981;">Rp <?= number_format($t['amount']) ?></span>
        <?php endif; ?>
    </td>
    <td>
        <?php 
        $statusColor = '';
        if ($t['status'] === 'paid' || $t['status'] === 'approved') {
            $statusColor = 'color:#10b981;';
        } elseif ($t['status'] === 'pending') {
            $statusColor = 'color:#f59e0b;';
        } elseif ($t['status'] === 'rejected') {
            $statusColor = 'color:#ef4444;';
        }
        ?>
        <span style="<?= $statusColor ?>font-weight:600;"><?= strtoupper($t['status']) ?></span>
    </td>
    <td><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<!-- LOAD MORE BUTTON -->
<div id="loadMoreContainer" style="text-align:center;margin-top:20px;padding-top:20px;border-top:1px solid #e2e8f0;">
    <button id="loadMoreBtn" 
            style="padding:12px 32px;background:#667eea;color:white;border:none;border-radius:6px;font-weight:600;cursor:pointer;font-size:14px;">
        Load More (50)
    </button>
    <div id="loadingIndicator" style="display:none;color:#64748b;margin-top:12px;">
        <i class="fa-solid fa-spinner fa-spin"></i> Loading...
    </div>
</div>

<script>
let offset = <?= count($transactions) ?>;
const filters = {
    t_search: '<?= esc($transactionSearch ?? '') ?>',
    t_event: '<?= esc($transactionEvent ?? '') ?>',
    t_method: '<?= esc($transactionMethod ?? '') ?>',
    t_status: '<?= esc($transactionStatus ?? '') ?>',
    t_date_from: '<?= esc($transactionDateFrom ?? '') ?>',
    t_date_to: '<?= esc($transactionDateTo ?? '') ?>'
};

document.getElementById('loadMoreBtn').addEventListener('click', async function() {
    const btn = this;
    const loading = document.getElementById('loadingIndicator');
    
    btn.style.display = 'none';
    loading.style.display = 'block';
    
    try {
        const params = new URLSearchParams({...filters, offset});
        const response = await fetch(`/admin/reports/load-more?${params}`);
        const data = await response.json();
        
        if (data.success && data.transactions.length > 0) {
            const tbody = document.getElementById('transactionTableBody');
            
            data.transactions.forEach(t => {
                const row = document.createElement('tr');
                
                // Type
                const typeColor = t.type === 'refund' ? '#ef4444' : '#10b981';
                const typeText = t.type === 'refund' ? 'REFUND' : 'PAYMENT';
                
                // Amount
                const amountColor = t.type === 'refund' ? '#ef4444' : '#10b981';
                const amountPrefix = t.type === 'refund' ? '- Rp' : 'Rp';
                
                // Status
                let statusColor = '';
                if (t.status === 'paid' || t.status === 'approved') statusColor = 'color:#10b981;';
                else if (t.status === 'pending') statusColor = 'color:#f59e0b;';
                else if (t.status === 'rejected') statusColor = 'color:#ef4444;';
                
                // Method
                const methodText = t.type === 'refund' ? '-' : t.method.toUpperCase();
                const methodColor = t.type === 'refund' ? '#94a3b8' : '';
                
                row.innerHTML = `
                    <td>${t.ticket_code}</td>
                    <td>${t.event_title}</td>
                    <td><span style="color:${typeColor};font-weight:700;">${typeText}</span></td>
                    <td><span style="color:${methodColor}">${methodText}</span></td>
                    <td><span style="color:${amountColor};">${amountPrefix} ${parseInt(t.amount).toLocaleString()}</span></td>
                    <td><span style="${statusColor}font-weight:600;">${t.status.toUpperCase()}</span></td>
                    <td>${new Date(t.created_at).toLocaleString('id-ID', {day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'})}</td>
                `;
                
                tbody.appendChild(row);
            });
            
            offset += data.transactions.length;
            
            if (data.hasMore) {
                btn.style.display = 'block';
                loading.style.display = 'none';
            } else {
                document.getElementById('loadMoreContainer').innerHTML = '<p style="color:#64748b;">No more transactions</p>';
            }
        } else {
            document.getElementById('loadMoreContainer').innerHTML = '<p style="color:#64748b;">No more transactions</p>';
        }
    } catch (error) {
        console.error('Error loading more:', error);
        btn.style.display = 'block';
        loading.style.display = 'none';
        alert('Error loading more transactions');
    }
});
</script>
</div>

</div>
</body>
</html>
