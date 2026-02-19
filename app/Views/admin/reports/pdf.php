<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Event Sales Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24px;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #64748b;
            font-size: 11px;
        }
        
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        
        .summary-item {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        
        .summary-item small {
            display: block;
            color: #64748b;
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        .summary-item h2 {
            font-size: 20px;
            color: #1e293b;
        }
        
        .summary-item.revenue h2 {
            color: #10b981;
        }
        
        .summary-item.remaining h2 {
            color: #f97316;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin: 25px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background: #667eea;
            color: white;
        }
        
        table th {
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
        }
        
        table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        
        table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        
        .text-success {
            color: #10b981;
            font-weight: 600;
        }
        
        .text-warning {
            color: #f97316;
            font-weight: 600;
        }
        
        .text-danger {
            color: #ef4444;
            font-weight: 600;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 9px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>📊 EVENT SALES REPORT</h1>
    <p>Generated on <?= $generatedDate ?></p>
    <?php
    $filters = [];
    if (!empty($transactionSearch)) $filters[] = "Search: " . esc($transactionSearch);
    if (!empty($transactionEventName)) $filters[] = "Event: " . esc($transactionEventName);
    if (!empty($transactionMethod)) $filters[] = "Method: " . strtoupper($transactionMethod);
    if (!empty($transactionStatus)) $filters[] = "Status: " . strtoupper($transactionStatus);
    if (!empty($transactionDateFrom)) $filters[] = "From: " . date('d M Y', strtotime($transactionDateFrom));
    if (!empty($transactionDateTo)) $filters[] = "To: " . date('d M Y', strtotime($transactionDateTo));
    if (!empty($filters)):
    ?>
    <p style="margin-top:8px;font-size:10px;color:#64748b;">
        <strong>Active Filters:</strong> <?= implode(' | ', $filters) ?>
    </p>
    <?php endif; ?>
</div>

<!-- SUMMARY -->
<div class="summary">
    <div class="summary-item revenue">
        <small>Total Revenue</small>
        <h2>Rp <?= number_format($totalRevenue, 0, ',', '.') ?></h2>
    </div>
    <div class="summary-item">
        <small>Total Sold Tickets</small>
        <h2><?= number_format($totalSold) ?></h2>
    </div>
    <div class="summary-item remaining">
        <small>Total Remaining Tickets</small>
        <h2><?= number_format($totalRemaining) ?></h2>
    </div>
</div>

<!-- EVENT PERFORMANCE -->
<div class="section-title">🎯 Event Performance</div>

<table>
<thead>
<tr>
    <th style="width: 30%;">Event</th>
    <th style="width: 15%;">Date</th>
    <th style="width: 10%;">Sold</th>
    <th style="width: 10%;">Remaining</th>
    <th style="width: 20%;">Revenue</th>
    <th style="width: 15%;">Status</th>
</tr>
</thead>
<tbody>
<?php if (empty($events)): ?>
<tr>
    <td colspan="6" style="text-align: center; color: #64748b; padding: 20px;">No events found</td>
</tr>
<?php else: ?>
<?php foreach ($events as $e): ?>
<tr>
    <td><strong><?= esc($e['title']) ?></strong></td>
    <td><?= date('d M Y', strtotime($e['event_date'])) ?></td>
    <td class="text-success"><?= number_format($e['sold']) ?></td>
    <td class="text-warning"><?= number_format($e['remaining']) ?></td>
    <td>Rp <?= number_format($e['revenue'], 0, ',', '.') ?></td>
    <td>
        <?= $e['ended']
            ? '<span class="text-danger">ENDED</span>'
            : '<span class="text-success">ONGOING</span>' ?>
    </td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>

<div class="page-break"></div>

<!-- TRANSACTIONS -->
<div class="section-title">📋 Transaction Detail</div>

<table>
<thead>
<tr>
    <th style="width: 15%;">Ticket Code</th>
    <th style="width: 23%;">Event</th>
    <th style="width: 10%;">Type</th>
    <th style="width: 10%;">Method</th>
    <th style="width: 16%;">Amount</th>
    <th style="width: 10%;">Status</th>
    <th style="width: 16%;">Date</th>
</tr>
</thead>
<tbody>
<?php if (empty($transactions)): ?>
<tr>
    <td colspan="7" style="text-align: center; color: #64748b; padding: 20px;">No transactions found</td>
</tr>
<?php else: ?>
<?php foreach ($transactions as $t): ?>
<tr>
    <td><?= esc($t['ticket_code']) ?></td>
    <td><?= esc($t['event_title']) ?></td>
    <td>
        <?php if ($t['type'] === 'refund'): ?>
            <span class="text-danger">REFUND</span>
        <?php else: ?>
            <span class="text-success">PAYMENT</span>
        <?php endif; ?>
    </td>
    <td><?= strtoupper($t['method']) ?></td>
    <td>
        <?php if ($t['type'] === 'refund'): ?>
            <span class="text-danger">-Rp <?= number_format(abs($t['amount']), 0, ',', '.') ?></span>
        <?php else: ?>
            <span>Rp <?= number_format($t['amount'], 0, ',', '.') ?></span>
        <?php endif; ?>
    </td>
    <td><?= strtoupper($t['status']) ?></td>
    <td><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>

<div class="footer">
    <p>Event Management System - Sales Report</p>
    <p>This is a computer-generated document and does not require a signature</p>
</div>

</body>
</html>
