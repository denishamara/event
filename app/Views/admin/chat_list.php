<!DOCTYPE html>
<html>
<head>
    <title>Chat List - Admin</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 32px;
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        }
        .chat-header h2 {
            color: white;
            margin-bottom: 8px;
            font-size: 32px;
        }
        .chat-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }
        .chat-item {
            background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        .chat-item:hover {
            transform: translateX(4px);
            border-color: #667eea;
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.2);
        }
        .chat-info {
            flex: 1;
        }
        .chat-user {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .chat-user-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            font-weight: 700;
        }
        .chat-description {
            color: #64748b;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #94a3b8;
        }
        .empty-state-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.4;
        }
        .empty-state-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #64748b;
        }
        .empty-state-text {
            font-size: 15px;
            color: #94a3b8;
        }
        .unread-badge {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }
        .chat-item.has-unread {
            border-color: #fca5a5;
            background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
        }
    </style>
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">
    <div class="chat-header">
        <h2>💬 User Chat Support</h2>
        <p>Manage and respond to user inquiries</p>
    </div>

    <?php if (empty($chats)): ?>
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon">💬</div>
                <div class="empty-state-title">No active chats</div>
                <div class="empty-state-text">Waiting for customer inquiries</div>
            </div>
        </div>
    <?php else: ?>
        <div style="display: grid; gap: 16px;">
            <?php foreach ($chats as $c): ?>
                <?php 
                    // Ambil jumlah pesan yang belum dibaca dari data real
                    $unreadCount = isset($c['unread_count']) ? (int)$c['unread_count'] : 0;
                    $hasUnread = $unreadCount > 0;
                ?>
                <div class="chat-item <?= $hasUnread ? 'has-unread' : '' ?>">
                    <div class="chat-info">
                        <div class="chat-user">
                            <div class="chat-user-icon">
                                <?= strtoupper(substr($c['user_name'], 0, 1)) ?>
                            </div>
                            <span><?= esc($c['user_name']) ?></span>
                            <?php if ($hasUnread): ?>
                                <span class="unread-badge">
                                    <span>🔔</span>
                                    <span><?= $unreadCount ?> unread</span>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="chat-description">
                            <span>💼</span>
                            <span>User support conversation</span>
                        </div>
                    </div>
                    <a class="btn" href="/admin/chat/<?= $c['id'] ?>">
                        👁️ Open Chat
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
