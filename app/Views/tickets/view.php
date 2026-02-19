<!DOCTYPE html>
<html>
<head>
    <title>Your Ticket</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="/assets/js/custom-alert.js"></script>
    <style>
        .ticket-container {
            max-width: 500px;
            margin: auto;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .ticket-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 32px;
            text-align: center;
        }
        .ticket-body {
            padding: 32px;
            text-align: center;
        }
        .qr-section {
            background: #f7fafc;
            padding: 24px;
            border-radius: 12px;
            margin: 20px 0;
        }
        .ticket-info {
            text-align: left;
            margin: 20px 0;
            padding: 20px;
            background: #f7fafc;
            border-radius: 12px;
        }
        .ticket-info p {
            margin: 12px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ticket-info b {
            color: #64748b;
        }
        .checkin-status {
            padding: 12px;
            border-radius: 8px;
            margin-top: 16px;
            font-weight: 600;
        }
        .checkin-status.checked-in {
            background: #dcfce7;
            color: #166534;
        }
        .checkin-status.not-checked-in {
            background: #fef9c3;
            color: #92400e;
        }
        .share-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            margin-top: 16px;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .share-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(16, 185, 129, 0.6);
        }
        .share-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            z-index: 99999;
            animation: fadeIn 0.3s ease;
        }
        .share-modal.show {
            display: flex !important;
            justify-content: center;
            align-items: center;
        }
        .share-content {
            background: white;
            border-radius: 28px;
            padding: 40px 35px 35px 35px;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
            animation: slideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            max-height: 90vh;
            overflow-y: auto;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(60px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .share-header {
            text-align: center;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 2px solid #f1f5f9;
        }
        .share-icon-main {
            width: 85px;
            height: 85px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 42px;
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.4);
            animation: bounceIn 0.6s ease;
        }
        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
        .share-header h3 {
            color: #0f172a;
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        .share-header p {
            color: #475569;
            font-size: 15px;
            line-height: 1.6;
            font-weight: 500;
        }
        .share-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }
        .share-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 18px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-decoration: none;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            position: relative;
            overflow: hidden;
        }
        .share-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .share-option:hover::before {
            opacity: 1;
        }
        .share-option:hover {
            border-color: #667eea;
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.25);
        }
        .share-option-icon {
            font-size: 36px;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }
        .share-option:hover .share-option-icon {
            transform: scale(1.15);
        }0f172a;
            font-size: 14t {
            color: #1e293b;
            font-size: 15px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        .share-link-section {
            background: linear-gradient(135deg, #f7fafc 0%, #f1f5f9 100%);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 20px;
            border: 2px solid #e2e8f0;
        }1e293b;
            font-size: 12px;
            margin-bottom: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .share-link-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #cbd5e1;
            border-radius: 10px;
            font-size: 13px;
            font-family: 'Courier New', monospace;
            background: white;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            color: #475569;
        }
        .share-link-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        .copy-link-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .copy-link-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
        }
        .copy-link-btn:active {
            transform: translateY(-1px);
        }
        .close-share-btn {
            width: 100%;
            padding: 14px;
            background: white;
            color: #64748b;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .close-share-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }
        @media (max-width: 480px) {
            .share-content {
                padding: 32px 24px 28px 24px;
                max-height: 85vh;
            }
            .share-header {
                margin-bottom: 24px;
                padding-bottom: 20px;
            }
            .share-icon-main {
                width: 70px;
                height: 70px;
                font-size: 36px;
                margin-bottom: 16px;
            }
            .share-header h3 {
                font-size: 22px;
            }
            .share-header p {
                font-size: 14px;
            }
            .share-options {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            .share-option {
                padding: 18px 12px;
            }
            .share-option-icon {
                font-size: 30px;
                margin-bottom: 10px;
            }
            .share-option-text {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">
    <div class="ticket-container">
        
        <div class="ticket-header">
            <h2>🎟️ Your Ticket</h2>
            <p style="margin-top:8px;opacity:0.9">Ticket Code: <?= esc($ticket['ticket_code']) ?></p>
        </div>

        <div class="ticket-body">
            
            <!-- QR CODE -->
            <?php if (!empty($ticket['qr_data'])): ?>
                <div class="qr-section">
                    <!-- Primary QR (generated locally) with fallback to external API -->
                    <img
                        id="qr-image"
                        src="/qr/generate/<?= $ticket['id'] ?>"
                        alt="QR Code"
                        style="max-width:280px;width:100%;height:auto;display:block;margin:0 auto"
                        onerror="this.onerror=null; this.src='https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=<?= urlencode($ticket['qr_data']) ?>';"
                    >
                    <p style="color:#64748b;margin-top:12px;font-size:14px">
                        📱 Show this QR code at the event gate
                    </p>
                    
                    <!-- QR Data Copy Section -->
                    <div style="margin-top:16px;padding:12px;background:#fff;border:2px dashed #e2e8f0;border-radius:8px">
                        <p style="font-size:12px;color:#64748b;margin-bottom:6px">
                            <b>QR Code Data:</b>
                        </p>
                        <input 
                            type="text" 
                            id="qr-data-field"
                            value="<?= esc($ticket['qr_data']) ?>" 
                            readonly
                            style="width:100%;padding:8px;font-size:11px;font-family:monospace;border:1px solid #e2e8f0;border-radius:4px;background:#f7fafc"
                            onclick="this.select()"
                        >
                        <button 
                            onclick="copyQrData()" 
                            class="btn"
                            style="margin-top:8px;width:100%"
                        >
                            📋 Copy QR Data for Verification
                        </button>
                        <p style="font-size:11px;color:#94a3b8;margin-top:8px;line-height:1.5">
                            💡 Admin can paste this data in "QR Scanner" page to verify ticket
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div style="padding:20px;background:#fef9c3;border-radius:8px;color:#92400e">
                    ⏳ QR Code will be generated after payment approval
                </div>
            <?php endif; ?>

            <!-- TICKET INFO -->
            <div class="ticket-info">
                <p>
                    <b>Event:</b>
                    <span><?= esc($event['title']) ?></span>
                </p>
                <p>
                    <b>Location:</b>
                    <span><?= esc($event['location']) ?></span>
                </p>
                <p>
                    <b>Date:</b>
                    <span><?= date('d M Y H:i', strtotime($event['event_date'])) ?></span>
                </p>
                <p>
                    <b>Price:</b>
                    <span style="color:#667eea;font-weight:600;">Rp <?= number_format($ticket['price'], 0, ',', '.') ?></span>
                </p>
                <?php if (isset($ticket['qty']) && $ticket['qty'] > 1): ?>
                <p>
                    <b>Quantity:</b>
                    <span style="color:#10b981;font-weight:700;font-size:16px;">📦 <?= $ticket['qty'] ?> Tiket</span>
                </p>
                <p>
                    <b>Total:</b>
                    <span style="color:#667eea;font-weight:700;font-size:16px;">Rp <?= number_format($ticket['price'] * $ticket['qty'], 0, ',', '.') ?></span>
                </p>
                <?php endif; ?>
                <?php if (!empty($ticket['phase']) && $ticket['phase'] !== 'Regular'): ?>
                <p>
                    <b>Phase:</b>
                    <span style="color:#10b981;font-weight:600;"><?= esc($ticket['phase']) ?></span>
                </p>
                <?php endif; ?>
            </div>

            <!-- CHECK-IN STATUS -->
            <?php if ($ticket['is_checked_in']): ?>
                <div class="checkin-status checked-in">
                    ✅ Checked in at: <?= date('d M Y H:i', strtotime($ticket['checked_in_at'])) ?>
                </div>
            <?php else: ?>
                <div class="checkin-status not-checked-in">
                    ⏳ Not checked in yet
                </div>
            <?php endif; ?>

            <!-- SHARE BUTTON -->
            <button class="share-btn" onclick="openShareModal()">
                <span>🔗</span>
                <span>Share Ticket</span>
            </button>

            <a href="/my-tickets" class="btn" style="margin-top:24px">
                ← Back to My Tickets
<div class="share-modal" id="shareModal">
    <div class="share-content">
        <div class="share-header">
            <div class="share-icon-main">🎫</div>
            <h3>Share Your Ticket</h3>
            <p>Share this ticket with friends or save for later</p>
        </div>

        <div class="share-options">
            <a href="#" class="share-option" onclick="shareWhatsApp(); return false;">
                <div class="share-option-icon">💬</div>
                <div class="share-option-text">WhatsApp</div>
            </a>
            
            <a href="#" class="share-option" onclick="shareEmail(); return false;">
                <div class="share-option-icon">📧</div>
                <div class="share-option-text">Email</div>
            </a>
            
            <a href="#" class="share-option" onclick="shareTelegram(); return false;">
                <div class="share-option-icon">✈️</div>
                <div class="share-option-text">Telegram</div>
            </a>
            
            <a href="#" class="share-option" onclick="downloadTicket(); return false;">
                <div class="share-option-icon">💾</div>
                <div class="share-option-text">Download</div>
            </a>
        </div>

        <div class="share-link-section">
            <div class="share-link-label">
                🔗 Ticket Link
            </div>
            <input 
                type="text" 
                id="shareLink" 
                value="<?= base_url('tickets/view/' . $ticket['id']) ?>" 
                readonly 
                class="share-link-input"
                onclick="this.select()"
            >
            <button class="copy-link-btn" onclick="copyShareLink()">
                📋 Copy Link
            </button>
        </div>

        <button class="close-share-btn" onclick="closeShareModal()">
            Close
        </button>
    </div>
</div>

<script>
// Ticket data
const ticketData = {
    title: '<?= esc($event['title']) ?>',
    date: '<?= date('d M Y H:i', strtotime($event['event_date'])) ?>',
    location: '<?= esc($event['location']) ?>',
    code: '<?= esc($ticket['ticket_code']) ?>',
    link: '<?= base_url('tickets/view/' . $ticket['id']) ?>'
};

function openShareModal() {
    console.log('Opening share modal');
    const modal = document.getElementById('shareModal');
    if (modal) {
        modal.classList.add('show');
        console.log('Modal shown');
    } else {
        console.error('Share modal not found');
    }
}

function closeShareModal() {
    const modal = document.getElementById('shareModal');
    if (modal) {
        modal.classList.remove('show');
    }
}

function shareWhatsApp() {
    const text = `🎫 *My Event Ticket*\n\n` +
                 `📌 Event: ${ticketData.title}\n` +
                 `📅 Date: ${ticketData.date}\n` +
                 `📍 Location: ${ticketData.location}\n` +
                 `🎟️ Ticket Code: ${ticketData.code}\n\n` +
                 `View ticket: ${ticketData.link}`;
    
    const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
}

function shareEmail() {
    const subject = `🎫 Event Ticket - ${ticketData.title}`;
    const body = `Hi!\n\n` +
                 `I'd like to share my event ticket with you:\n\n` +
                 `Event: ${ticketData.title}\n` +
                 `Date: ${ticketData.date}\n` +
                 `Location: ${ticketData.location}\n` +
                 `Ticket Code: ${ticketData.code}\n\n` +
                 `View full ticket details: ${ticketData.link}\n\n` +
                 `See you at the event!`;
    
    const url = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    window.location.href = url;
}

function shareTelegram() {
    const text = `🎫 My Event Ticket\n\n` +
                 `📌 ${ticketData.title}\n` +
                 `📅 ${ticketData.date}\n` +
                 `📍 ${ticketData.location}\n` +
                 `🎟️ ${ticketData.code}\n\n` +
                 `${ticketData.link}`;
    
    const url = `https://t.me/share/url?url=${encodeURIComponent(ticketData.link)}&text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
}

function downloadTicket() {
    customAlert(
        'Download QR code by right-clicking the QR image and selecting "Save Image As..."',
        'Download Ticket',
        '💾',
        'info'
    );
}

function copyShareLink() {
    const input = document.getElementById('shareLink');
    input.select();
    input.setSelectionRange(0, 99999);
    
    try {
        document.execCommand('copy');
        
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '✅ Link Copied!';
        btn.style.background = '#10b981';
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '';
        }, 2000);
        
        customAlert(
            'Ticket link has been copied to clipboard!',
            'Success',
            '✅',
            'success'
        );
    } catch (err) {
        customAlert(
            'Failed to copy link. Please copy manually.',
            'Error',
            '❌',
            'error'
        );
    }
}

// Initialize modal close on click outside when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const shareModal = document.getElementById('shareModal');
    if (shareModal) {
        shareModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeShareModal();
            }
        });
        console.log('Share modal event listener added');
    } else {
        console.error('Share modal not found on DOM load');
    }
});
</script>

<script>
function copyQrData() {
    const field = document.getElementById('qr-data-field');
    const qrData = field.value;
    
    field.select();
    field.setSelectionRange(0, 99999); // For mobile
    
    try {
        document.execCommand('copy');
        
        // Show success message
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '✅ Copied!';
        btn.style.background = '#10b981';
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '';
        }, 2000);
        
        // Show alert with instructions
        setTimeout(() => {
            customAlert(
                'QR Data copied!\n\nNext steps:\n1. Go to Admin > QR Scanner\n2. Paste the data in the input field\n3. Click "Verify"\n\nQR Data copied:\n' + qrData.substring(0, 30) + '...',
                'Success',
                '✅',
                'success'
            );
        }, 100);
        
    } catch (err) {
        customAlert(
            'Failed to copy. Please select and copy manually.',
            'Error',
            '❌',
            'error'
        );
    }
}
</script>

</body>
</html>
