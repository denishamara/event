<!DOCTYPE html>
<html>
<head>
    <title>Manage Events - Admin</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="/assets/js/custom-alert.js"></script>
    <style>
        .filter-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0;
        }
        
        .filter-tab {
            padding: 12px 24px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            color: #64748b;
            transition: all 0.3s;
            position: relative;
            bottom: -2px;
        }
        
        .filter-tab:hover {
            color: #475569;
        }
        
        .filter-tab.active {
            color: #6366f1;
            border-bottom-color: #6366f1;
        }
        
        .event-section {
            display: none;
        }
        
        .event-section.active {
            display: block;
        }
    </style>
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div>
            <h2>Manage Events</h2>
            <p style="color:#64748b;font-size:16px;">
                Create and manage your events
            </p>
        </div>
        <a class="btn" href="/admin/events/create">Create New Event</a>
    </div>

    <!-- FILTER TABS -->
    <div class="filter-tabs">
        <button class="filter-tab active" onclick="switchTab('upcoming')">
            📅 Upcoming Events
        </button>
        <button class="filter-tab" onclick="switchTab('history')">
            📜 History (Past Events)
        </button>
    </div>

    <?php 
    $now = strtotime('now');
    $upcomingEvents = array_filter($events, function($e) use ($now) {
        return strtotime($e['event_date']) >= $now;
    });
    $pastEvents = array_filter($events, function($e) use ($now) {
        return strtotime($e['event_date']) < $now;
    });
    ?>

    <!-- UPCOMING EVENTS SECTION -->
    <div id="upcoming-section" class="event-section active">
        <?php if (empty($upcomingEvents)): ?>
            <div class="card">
                <div style="text-align:center;padding:60px;color:#94a3b8;">
                    <p style="font-size:20px;font-weight:600;">No upcoming events</p>
                    <p style="font-size:14px;">All your events have passed or you haven't created any yet</p>
                    <a class="btn mt-4" href="/admin/events/create">Create Event</a>
                </div>
            </div>
        <?php else: ?>
            <div class="event-grid">
                <?php foreach ($upcomingEvents as $e): ?>

                <div class="event-card" style="position:relative;">

                    <!-- 🔖 LABEL -->
                    <?php if ($e['quota'] <= 0): ?>
                        <div style="
                            position:absolute;
                            top:12px;
                            right:12px;
                            background:#ef4444;
                            color:#fff;
                            padding:6px 12px;
                            border-radius:999px;
                            font-size:12px;
                            font-weight:700;
                            z-index:10;
                        ">
                            SOLD OUT
                        </div>
                    <?php else: ?>
                        <?php 
                        $phase = $e['price_phase'] ?? 'Regular';
                        $bgColor = '#64748b'; // Default untuk Regular
                        
                        if ($phase === 'Early Bird') {
                            $bgColor = '#10b981'; // Hijau
                        } elseif ($phase === 'Presale') {
                            $bgColor = '#f59e0b'; // Kuning/Orange
                        } elseif ($phase === 'Last Minute') {
                            $bgColor = '#ef4444'; // Merah
                        }
                        ?>
                        <div style="
                            position:absolute;
                            top:12px;
                            right:12px;
                            background:<?= $bgColor ?>;
                            color:#fff;
                            padding:6px 12px;
                            border-radius:999px;
                            font-size:12px;
                            font-weight:700;
                            z-index:10;
                        ">
                            <?= strtoupper(esc($phase)) ?>
                        </div>
                    <?php endif; ?>

                    <!-- HEADER -->
                    <div class="event-header">
                        <h3><?= esc($e['title']) ?></h3>
                        <div class="event-info" style="color:rgba(255,255,255,0.9);">
                            <?= esc($e['location']) ?>
                        </div>
                        <div class="event-info" style="color:rgba(255,255,255,0.9);">
                            <?= date('M d, Y - H:i', strtotime($e['event_date'])) ?>
                        </div>
                    </div>

                    <!-- BODY -->
                    <div class="event-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">

                            <!-- BASE PRICE -->
                            <div style="background:#f7fafc;padding:12px;border-radius:8px;text-align:center;">
                                <div style="font-size:20px;font-weight:700;color:#64748b;">
                                    Rp <?= number_format($e['price']) ?>
                                </div>
                                <div style="color:#64748b;font-size:12px;">Base Price</div>
                            </div>

                            <!-- ACTIVE PRICE -->
                            <div style="background:#ecfdf5;padding:12px;border-radius:8px;text-align:center;">
                                <div style="font-size:20px;font-weight:700;color:#10b981;">
                                    Rp <?= number_format($e['display_price'] ?? $e['price']) ?>
                                </div>
                                <div style="color:#047857;font-size:12px;">
                                    <?= esc($e['price_phase'] ?? 'Regular') ?>
                                </div>
                            </div>

                        </div>

                        <div style="text-align:center;margin-top:12px;padding:14px;background:#f8fafc;border-radius:8px;">
                            <div style="color:#64748b;font-size:14px;margin-bottom:6px;font-weight:600;">Stok Tiket</div>
                            <div style="font-size:32px;font-weight:700;color:#1e293b;">
                                <?= esc($e['quota']) ?>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="event-footer" style="display:flex;gap:8px;">
                        <a class="btn" href="/admin/events/edit/<?= $e['id'] ?>" style="flex:1;text-align:center;">
                            Edit
                        </a>
                        <a class="btn red"
                           href="#"
                           onclick="confirmDelete(<?= $e['id'] ?>); return false;"
                           style="flex:1;text-align:center;">
                            Delete
                        </a>
                    </div>

                </div>

            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- HISTORY SECTION -->
    <div id="history-section" class="event-section">
        <?php if (empty($pastEvents)): ?>
            <div class="card">
                <div style="text-align:center;padding:60px;color:#94a3b8;">
                    <p style="font-size:20px;font-weight:600;">No past events</p>
                    <p style="font-size:14px;">Events that have passed will appear here</p>
                </div>
            </div>
        <?php else: ?>
            <div class="event-grid">
                <?php foreach ($pastEvents as $e): ?>

                    <div class="event-card" style="position:relative;opacity:0.85;">

                        <!-- 🔖 LABEL -->
                        <div style="
                            position:absolute;
                            top:12px;
                            right:12px;
                            background:#64748b;
                            color:#fff;
                            padding:6px 12px;
                            border-radius:999px;
                            font-size:12px;
                            font-weight:700;
                            z-index:10;
                        ">
                            ENDED
                        </div>

                        <!-- HEADER -->
                        <div class="event-header">
                            <h3><?= esc($e['title']) ?></h3>
                            <div class="event-info" style="color:rgba(255,255,255,0.9);">
                                <?= esc($e['location']) ?>
                            </div>
                            <div class="event-info" style="color:rgba(255,255,255,0.9);">
                                <?= date('M d, Y - H:i', strtotime($e['event_date'])) ?>
                            </div>
                        </div>

                        <!-- BODY -->
                        <div class="event-body">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">

                                <!-- BASE PRICE -->
                                <div style="background:#f7fafc;padding:12px;border-radius:8px;text-align:center;">
                                    <div style="font-size:20px;font-weight:700;color:#64748b;">
                                        Rp <?= number_format($e['price']) ?>
                                    </div>
                                    <div style="color:#64748b;font-size:12px;">Base Price</div>
                                </div>

                                <!-- ACTIVE PRICE -->
                                <div style="background:#f1f5f9;padding:12px;border-radius:8px;text-align:center;">
                                    <div style="font-size:20px;font-weight:700;color:#64748b;">
                                        Rp <?= number_format($e['display_price'] ?? $e['price']) ?>
                                    </div>
                                    <div style="color:#64748b;font-size:12px;">
                                        <?= esc($e['price_phase'] ?? 'Regular') ?>
                                    </div>
                                </div>

                            </div>

                            <div style="text-align:center;margin-top:12px;padding:14px;background:#f8fafc;border-radius:8px;">
                                <div style="color:#64748b;font-size:14px;margin-bottom:6px;font-weight:600;">Stok Tiket</div>
                                <div style="font-size:32px;font-weight:700;color:#1e293b;">
                                    <?= esc($e['quota']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="event-footer" style="display:flex;gap:8px;">
                            <a class="btn" href="/admin/events/edit/<?= $e['id'] ?>" style="flex:1;text-align:center;">
                                View
                            </a>
                            <a class="btn red"
                               href="#"
                               onclick="confirmDelete(<?= $e['id'] ?>); return false;"
                               style="flex:1;text-align:center;">
                                Delete
                            </a>
                        </div>

                    </div>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<script>
function switchTab(tab) {
    // Update tabs
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    event.target.classList.add('active');
    
    // Update sections
    document.querySelectorAll('.event-section').forEach(s => s.classList.remove('active'));
    document.getElementById(tab + '-section').classList.add('active');
}

function confirmDelete(eventId) {
    customConfirm(
        'Are you sure you want to delete this event? This action cannot be undone.',
        'Delete Event',
        function() {
            window.location.href = '/admin/events/delete/' + eventId;
        },
        null,
        'Yes, Delete',
        'Cancel'
    );
}
</script>

</body>
</html>
