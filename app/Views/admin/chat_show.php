<!DOCTYPE html>
<html>
<head>
    <title>Chat Room</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        .chat-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px 32px;
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chat-header h2 {
            color: white;
            margin: 0;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(-4px);
        }
        .messages-container {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            min-height: 500px;
            max-height: 600px;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 2px solid #e2e8f0;
        }
        .messages-container::-webkit-scrollbar {
            width: 8px;
        }
        .messages-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .messages-container::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }
        .messages-container::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
        .message {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            animation: messageSlide 0.3s ease;
        }
        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .message.admin {
            align-items: flex-end;
        }
        .message.user {
            align-items: flex-start;
        }
        .message-header {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .message-bubble {
            padding: 14px 18px;
            border-radius: 16px;
            max-width: 70%;
            word-wrap: break-word;
            line-height: 1.6;
            font-size: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .message.admin .message-bubble {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }
        .message.user .message-bubble {
            background: #f1f5f9;
            color: #2d3748;
            border: 1px solid #e2e8f0;
            border-bottom-left-radius: 4px;
        }
        .empty-chat {
            text-align: center;
            padding: 80px 20px;
            color: #94a3b8;
        }
        .empty-chat-icon {
            font-size: 80px;
            margin-bottom: 16px;
            opacity: 0.3;
        }
        .empty-chat-text {
            font-size: 16px;
            color: #64748b;
        }
        .reply-container {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 2px solid #e2e8f0;
        }
        .reply-form {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }
        .reply-form textarea {
            flex: 1;
            min-height: 60px;
            max-height: 150px;
            resize: vertical;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        .reply-form textarea:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        .reply-form button {
            padding: 14px 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .reply-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">
    <div class="chat-container">
        <div class="chat-header">
            <h2>
                <span>💬</span>
                <span>Chat Room</span>
            </h2>
        </div>

        <div class="messages-container">
            <?php if (empty($messages)): ?>
                <div class="empty-chat">
                    <div class="empty-chat-icon">💭</div>
                    <div class="empty-chat-text">No messages yet. Start the conversation!</div>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $m): ?>
                    <?php 
                        $isAdmin = ($m['sender_id'] == session()->get('user_id'));
                        $messageClass = $isAdmin ? 'admin' : 'user';
                    ?>
                    <div class="message <?= $messageClass ?>">
                        <div class="message-header">
                            <?php if ($isAdmin): ?>
                                <span>👤</span>
                                <span>You (Admin)</span>
                            <?php else: ?>
                                <span>👥</span>
                                <span>User</span>
                            <?php endif; ?>
                        </div>
                        <div class="message-bubble">
                            <?= nl2br(esc($m['message'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="reply-container">
            <form method="post" action="/admin/chat/send" class="reply-form">
                <?= csrf_field() ?>
                <input type="hidden" name="chat_id" value="<?= $chat_id ?>">
                <textarea 
                    name="message" 
                    placeholder="Type your message here..." 
                    required></textarea>
                <button type="submit">
                    <span>✉️</span>
                    <span>Send</span>
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
