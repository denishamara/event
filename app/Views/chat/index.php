<!DOCTYPE html>
<html>
<head>
    <title>Chat Support - Event App</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">
    <h2>💬 Chat Support</h2>
    <p style="color: rgba(255,255,255,0.9); font-size: 16px; margin-bottom: 32px;">Get help from our support team</p>

    <div class="card">
        <h3 style="margin-bottom: 16px;">📝 Conversation</h3>
        
        <div class="chat-box">
            <?php if (empty($messages)): ?>
                <div style="text-align: center; padding: 40px; color: #94a3b8;">
                    <p style="font-size: 16px; margin-bottom: 8px;">No messages yet</p>
                    <p style="font-size: 14px;">Start the conversation by sending a message below</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $m): ?>
                    <div class="chat-message <?= $m['sender_id']==session()->get('user_id') ? 'me' : 'other' ?>">
                        <b><?= $m['sender_id']==session()->get('user_id') ? 'You' : '👥 Support' ?></b>
                        <span><?= nl2br(esc($m['message'])) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 16px;">✏️ Send Message</h3>
        <form method="post" action="/chat/send">
            <?= csrf_field() ?>
            <input type="hidden" name="chat_id" value="<?= $chat_id ?>">
            <textarea name="message" placeholder="Type your message here..." required style="margin-top: 0;"></textarea>
            <button class="btn" style="width: 100%;">📫 Send Message</button>
        </form>
    </div>
</div>

</body>
</html>
