<!DOCTYPE html>
<html>
<head>
    <title>QR Code Verifier - Admin</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="/assets/js/custom-alert.js"></script>
    <style>
        .scanner-container {
            max-width: 800px;
            margin: auto;
        }
        .scanner-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 32px;
            border-radius: 16px 16px 0 0;
            text-align: center;
        }
        .scanner-body {
            background: white;
            padding: 32px;
            border-radius: 0 0 16px 16px;
        }
        .scan-input {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .scan-input input {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
        }
        .scan-input button {
            padding: 12px 24px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .scan-input button:hover {
            background: #5568d3;
        }
        .result-box {
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 24px;
        }
        .result-box.valid {
            background: #dcfce7;
            border: 2px solid #10b981;
        }
        .result-box.invalid {
            background: #fee2e2;
            border: 2px solid #ef4444;
        }
        .result-box.already_used {
            background: #fef9c3;
            border: 2px solid #f59e0b;
        }
        .result-message {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .ticket-detail {
            background: white;
            padding: 16px;
            border-radius: 8px;
            margin-top: 16px;
        }
        .ticket-detail p {
            margin: 8px 0;
            display: flex;
            justify-content: space-between;
        }
        .checkin-btn {
            width: 100%;
            padding: 16px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 16px;
        }
        .checkin-btn:hover {
            background: #059669;
        }
        .checkin-btn:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
        }
        #camera-preview {
            width: 100%;
            max-width: 400px;
            height: 300px;
            background: #f1f5f9;
            border-radius: 8px;
            margin: 20px auto;
            display: none;
        }
        .camera-controls {
            text-align: center;
            margin: 16px 0;
        }
    </style>
</head>
<body>

<?= view('partials/sidebar') ?>

<div class="content">
    <div class="scanner-container">
        
        <div class="scanner-header">
            <h2>📷 QR Code Scanner</h2>
            <p style="margin-top:8px;opacity:0.9">Scan ticket QR code for validation</p>
        </div>

        <div class="scanner-body">
            
            <!-- MANUAL INPUT -->
            <form action="/admin/qr/verify" method="get">
                <div class="scan-input">
                    <input 
                        type="text" 
                        name="qr_data" 
                        id="qr_input"
                        placeholder="Paste QR code here..." 
                        value="<?= esc($qrData ?? '') ?>"
                        autofocus
                    >
                    <button type="submit">Verify</button>
                </div>
            </form>

            <div style="margin-bottom:24px;padding:12px;background:#f7fafc;border-radius:8px;font-size:13px;color:#64748b">
                💡 <b>Tip:</b> Copy QR data dari tiket, lalu paste di field di atas. Format: <code style="background:#e2e8f0;padding:2px 6px;border-radius:4px">TKT-{id}-{uuid}-{hash}</code>
            </div>

            <!-- CAMERA SCAN (Optional - requires additional JS library) -->
            <div class="camera-controls">
                <button type="button" class="btn" onclick="toggleCamera()">
                    📷 Use Camera Scanner
                </button>
            </div>
            <div id="camera-preview"></div>

            <!-- RESULT -->
            <?php if ($result): ?>
                <div class="result-box <?= esc($result['status']) ?>">
                    <div class="result-message">
                        <?= $result['message'] ?>
                    </div>

                    <?php if (isset($result['ticket'])): ?>
                        <div class="ticket-detail">
                            <p>
                                <b>Ticket Code:</b>
                                <span><?= esc($result['ticket']['ticket_code']) ?></span>
                            </p>
                            <p>
                                <b>Event:</b>
                                <span><?= esc($result['ticket']['event_title']) ?></span>
                            </p>
                            <p>
                                <b>Event Date:</b>
                                <span><?= date('d M Y H:i', strtotime($result['ticket']['event_date'])) ?></span>
                            </p>
                            <p>
                                <b>Customer:</b>
                                <span><?= esc($result['ticket']['user_name']) ?></span>
                            </p>
                            <?php if (isset($result['ticket']['qty']) && $result['ticket']['qty'] > 1): ?>
                            <p>
                                <b>Quantity:</b>
                                <span style="color:#10b981;font-weight:700;font-size:18px">📦 <?= $result['ticket']['qty'] ?> Tickets</span>
                            </p>
                            <p>
                                <b>Price per ticket:</b>
                                <span>Rp <?= number_format($result['ticket']['price'], 0, ',', '.') ?></span>
                            </p>
                            <p>
                                <b>Total Price:</b>
                                <span style="color:#667eea;font-weight:700;font-size:18px">Rp <?= number_format($result['ticket']['price'] * $result['ticket']['qty'], 0, ',', '.') ?></span>
                            </p>
                            <?php else: ?>
                            <p>
                                <b>Price:</b>
                                <span>Rp <?= number_format($result['ticket']['price'], 0, ',', '.') ?></span>
                            </p>
                            <?php endif; ?>
                            <?php if (!empty($result['ticket']['phase']) && $result['ticket']['phase'] !== 'Regular'): ?>
                            <p>
                                <b>Phase:</b>
                                <span style="color:#10b981;font-weight:600"><?= esc($result['ticket']['phase']) ?></span>
                            </p>
                            <?php endif; ?>
                        </div>

                        <!-- CHECK IN BUTTON -->
                        <?php if ($result['status'] === 'valid'): ?>
                            <button 
                                type="button" 
                                class="checkin-btn" 
                                onclick="performCheckin('<?= esc($qrData) ?>')"
                            >
                                ✅ CHECK IN NOW
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- SUCCESS SOUND -->
                <?php if ($result['status'] === 'valid'): ?>
                    <audio id="success-sound" autoplay>
                        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFAxMouLusmsaCD2V2/K6axiGQZXy" type="audio/wav">
                    </audio>
                <?php endif; ?>
            <?php endif; ?>

            <!-- INSTRUCTIONS -->
            <div style="margin-top:32px;padding:20px;background:#f7fafc;border-radius:8px">
                <h3 style="margin-bottom:12px">📋 Instructions:</h3>
                <ol style="margin-left:20px;line-height:1.8">
                    <li>Type or paste the QR code data into the input field</li>
                    <li>Click "Verify" to check ticket validity</li>
                    <li>If valid, click "CHECK IN NOW" to mark attendance</li>
                    <li>Or use camera scanner for quick scanning</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<script>
function performCheckin(qrData) {
    customConfirm(
        'Are you sure you want to check in this ticket?',
        'Confirm Check-in',
        function() {
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = '⏳ Processing...';

            fetch('/admin/qr/checkin', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'qr_data=' + encodeURIComponent(qrData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    customAlert(
                        data.message,
                        'Success',
                        '✅',
                        'success'
                    );
                    setTimeout(() => location.reload(), 1500);
                } else {
                    customAlert(
                        data.message,
                        'Error',
                        '❌',
                        'error'
                    );
                    btn.disabled = false;
                    btn.textContent = '✅ CHECK IN NOW';
                }
            })
            .catch(error => {
                customAlert(
                    'Error: ' + error,
                    'Error',
                    '❌',
                    'error'
                );
                btn.disabled = false;
                btn.textContent = '✅ CHECK IN NOW';
            });
        },
        null,
        'Yes, Check In',
        'Cancel'
    );
}

function toggleCamera() {
    customAlert(
        'Camera scanner feature requires additional setup.\n\nFor now, please use:\n1. Manual input\n2. External QR scanner app\n3. Or integrate html5-qrcode library for camera scanning',
        'Camera Scanner',
        '📷',
        'info'
    );
}
</script>

</body>
</html>
