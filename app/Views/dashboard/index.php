<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 32px;
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        }
        .welcome-banner h2 {
            color: white;
            margin-bottom: 8px;
            font-size: 32px;
        }
        .welcome-banner p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }
        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
        }
        .section-title .icon {
            font-size: 28px;
        }
        .event-item {
            padding: 20px;
            background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            transition: all 0.3s ease;
        }
        .event-item:hover {
            transform: translateX(4px);
            border-color: #667eea;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
        }
        .event-info {
            flex: 1;
        }
        .event-title {
            font-weight: 600;
            color: #2d3748;
            font-size: 18px;
            display: block;
            margin-bottom: 6px;
        }
        .event-location {
            color: #64748b;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .ticket-item {
            padding: 18px 20px;
            background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }
        .ticket-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
        }
        .ticket-code {
            font-weight: 600;
            color: #2d3748;
            font-size: 16px;
            font-family: 'Courier New', monospace;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        .empty-state-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #64748b;
        }
        .empty-state-text {
            font-size: 14px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">

    <!-- ================= USER DASHBOARD ================= -->
    <?php if (session()->get('role') === 'user'): ?>

        <div class="welcome-banner">
            <h2>👋 Welcome, <?= session()->get('name') ?></h2>
            <p>Browse events and buy tickets easily</p>
        </div>

        <div class="card">
            <div class="section-title">
                <span class="icon">🎉</span>
                <span>Available Events</span>
            </div>

            <?php if (empty($events)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🎫</div>
                    <div class="empty-state-title">No events available</div>
                    <div class="empty-state-text">Check back later for upcoming events</div>
                </div>
            <?php endif; ?>

            <?php foreach ($events as $e): ?>
                <div class="event-item">
                    <div class="event-info">
                        <span class="event-title"><?= esc($e['title']) ?></span>
                        <span class="event-location">
                            <span>📍</span>
                            <span><?= esc($e['location']) ?></span>
                        </span>
                    </div>
                    <a class="btn" href="/events/<?= $e['id'] ?>">View Detail</a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card">
            <div class="section-title">
                <span class="icon">🎫</span>
                <span>My Tickets</span>
            </div>

            <?php if (empty($myTickets)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🎟️</div>
                    <div class="empty-state-title">No tickets yet</div>
                    <div class="empty-state-text">Purchase tickets to your favorite events</div>
                </div>
            <?php endif; ?>

            <?php foreach ($myTickets as $t): ?>
                <div class="ticket-item">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                            <span class="ticket-code"><?= esc($t['ticket_code']) ?></span>
                            <span class="badge <?= $t['status'] ?>">
                                <?= strtoupper($t['status']) ?>
                            </span>
                        </div>
                        <?php if (isset($t['event_title'])): ?>
                            <div style="font-weight: 600; color: #2d3748; font-size: 15px; margin-bottom: 4px;">
                                🎉 <?= esc($t['event_title']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($t['purchase_date']) || isset($t['created_at'])): ?>
                            <div style="color: #64748b; font-size: 13px;">
                                📅 Purchased: <?= date('M d, Y', strtotime($t['purchase_date'] ?? $t['created_at'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

    <!-- ================= ADMIN DASHBOARD ================= -->
    <?php if (session()->get('role') === 'admin'): ?>

        <div class="welcome-banner">
            <h2>⚙️ Admin Dashboard</h2>
            <p>Manage events, tickets, and support all in one place</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>🎉 Total Events</h3>
                <div class="stat-value"><?= $totalEvents ?></div>
                <p style="color: #64748b; font-size: 14px;">Events created</p>
            </div>
            <div class="stat-card">
                <h3>🎫 Tickets Sold</h3>
                <div class="stat-value"><?= $totalTicketsSold ?></div>
                <p style="color: #64748b; font-size: 14px;">Total ticket sales</p>
            </div>
            <div class="stat-card">
                <h3>💬 Chat Rooms</h3>
                <div class="stat-value"><?= $totalChats ?></div>
                <p style="color: #64748b; font-size: 14px;">Active conversations</p>
            </div>
        </div>

        <div class="card">
            <div class="section-title">
                <span class="icon">⚡</span>
                <span>Quick Actions</span>
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 16px;">
                <a class="btn" href="/admin/events/create">➕ Create Event</a>
                <a class="btn green" href="/admin/events">📋 Manage Events</a>
                <a class="btn" href="/admin/chats">💬 Chat Support</a>
            </div>
        </div>

    <?php endif; ?>

    <!-- ================= AGENT DASHBOARD ================= -->
    <?php if (session()->get('role') === 'agent'): ?>

        <div class="welcome-banner">
            <h2>💬 Agent Dashboard</h2>
            <p>Handle customer support chats and help users with their questions</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>💬 Active Chats</h3>
                <div class="stat-value"><?= count($chats ?? []) ?></div>
                <p style="color: #64748b; font-size: 14px;">Conversations to handle</p>
            </div>
        </div>

        <div class="card">
            <div class="section-title">
                <span class="icon">📋</span>
                <span>Chat Rooms</span>
            </div>

            <?php if (empty($chats)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">💬</div>
                    <div class="empty-state-title">No chats available</div>
                    <div class="empty-state-text">Waiting for customer inquiries</div>
                </div>
            <?php else: ?>
                <?php foreach ($chats as $c): ?>
                    <div class="event-item">
                        <span style="font-weight: 600; color: #2d3748;">
                            💬 Chat Room #<?= $c['id'] ?>
                        </span>
                        <a class="btn" href="/admin/chat/<?= $c['id'] ?>">Open Chat</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</div>

</body>
</html>
