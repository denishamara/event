<div class="sidebar">
    <h3>
        <i class="fa-solid fa-ticket"></i>
        EVENT APP
    </h3>

    <a href="/dashboard" class="<?= uri_string() === 'dashboard' ? 'active' : '' ?>">
        <i class="fa-solid fa-chart-line"></i>
        <span>Dashboard</span>
    </a>

    <a href="/events" class="<?= uri_string() === 'events' ? 'active' : '' ?>">
        <i class="fa-solid fa-calendar-days"></i>
        <span>Events</span>
    </a>

    <a href="/events/favorites"
       class="<?= uri_string() === 'events/favorites' ? 'active' : '' ?>">
        <i class="fa-solid fa-heart"></i>
        <span>My Favorites</span>
    </a>

    <?php if (session()->get('role') === 'user'): ?>
        <a href="/my-tickets" class="<?= uri_string() === 'my-tickets' ? 'active' : '' ?>">
            <i class="fa-solid fa-ticket-simple"></i>
            <span>My Tickets</span>
        </a>

        <a href="/chat" class="<?= uri_string() === 'chat' ? 'active' : '' ?>">
            <i class="fa-solid fa-comments"></i>
            <span>Chat Support</span>
        </a>
    <?php endif; ?>

    <?php if (session()->get('role') === 'admin' || session()->get('role') === 'agent'): ?>
        <?php $unreadChatsCount = get_unread_chats_count(); ?>
        <a href="/admin/chats" class="<?= uri_string() === 'admin/chats' ? 'active' : '' ?>">
            <i class="fa-solid fa-headset"></i>
            <span>User Chats</span>
            <?php if ($unreadChatsCount > 0): ?>
                <span class="notification-badge"><?= $unreadChatsCount ?></span>
            <?php endif; ?>
        </a>
    <?php endif; ?>

    <?php if (session()->get('role') === 'admin'): ?>
        <?php $pendingPaymentsCount = get_pending_payments_count(); ?>
        <a href="/admin/payments/pending" class="<?= uri_string() === 'admin/payments/pending' ? 'active' : '' ?>">
            <i class="fa-solid fa-clock"></i>
            <span>Pending Payments</span>
            <?php if ($pendingPaymentsCount > 0): ?>
                <span class="notification-badge"><?= $pendingPaymentsCount ?></span>
            <?php endif; ?>
        </a>

        <a href="/admin/qr/verify" class="<?= strpos(uri_string(), 'admin/qr') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-qrcode"></i>
            <span>QR Scanner</span>
        </a>

        <a href="/admin/reports" class="<?= uri_string() === 'admin/reports' ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-column"></i>
            <span>Sales Report</span>
        </a>
        <?php $pendingRefundsCount = get_pending_refunds_count(); ?>
        <a href="/admin/refunds" class="<?= uri_string() === 'admin/refunds' ? 'active' : '' ?>">
            <i class="fa-solid fa-money-bill-transfer"></i>
            <span>Refund Requests</span>
            <?php if ($pendingRefundsCount > 0): ?>
                <span class="notification-badge"><?= $pendingRefundsCount ?></span>
            <?php endif; ?>
        </a>
        <a href="/admin/events" class="<?= uri_string() === 'admin/events' ? 'active' : '' ?>">
            <i class="fa-solid fa-gear"></i>
            <span>Manage Events</span>
        </a>
    <?php endif; ?>

    <a href="/logout" style="margin-top:auto;">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Logout</span>
    </a>
</div>
