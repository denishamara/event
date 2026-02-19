/**
 * Custom Alert & Confirm Dialog
 * Modern and beautiful popup replacement for alert() and confirm()
 */

// Create modal HTML if not exists
function initCustomAlert() {
    if (document.getElementById('customAlertModal')) return;
    
    const modalHTML = `
        <div class="custom-modal-overlay" id="customAlertModal">
            <div class="custom-modal">
                <div class="custom-modal-icon" id="customModalIcon">✓</div>
                <h3 class="custom-modal-title" id="customModalTitle">Success</h3>
                <p class="custom-modal-message" id="customModalMessage">Operation completed successfully</p>
                <div class="custom-modal-buttons" id="customModalButtons">
                    <button type="button" class="custom-btn custom-btn-primary" onclick="closeCustomAlert()">OK</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Add CSS
    if (!document.getElementById('customAlertStyles')) {
        const style = document.createElement('style');
        style.id = 'customAlertStyles';
        style.textContent = `
            .custom-modal-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(5px);
                z-index: 99999;
                animation: fadeIn 0.3s ease;
            }
            
            .custom-modal-overlay.show {
                display: flex !important;
                justify-content: center;
                align-items: center;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            .custom-modal {
                background: white;
                border-radius: 20px;
                padding: 35px 30px;
                max-width: 420px;
                width: 90%;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                animation: slideDown 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                position: relative;
            }
            
            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-50px) scale(0.8);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            
            .custom-modal-icon {
                width: 80px;
                height: 80px;
                margin: 0 auto 20px;
                background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 40px;
                animation: bounce 0.6s ease;
            }
            
            .custom-modal-icon.error {
                background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            }
            
            .custom-modal-icon.warning {
                background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            }
            
            .custom-modal-icon.info {
                background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            }
            
            @keyframes bounce {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }
            
            .custom-modal-title {
                text-align: center;
                color: #1e293b;
                font-size: 22px;
                font-weight: 700;
                margin-bottom: 12px;
            }
            
            .custom-modal-message {
                text-align: center;
                color: #64748b;
                font-size: 15px;
                line-height: 1.6;
                margin-bottom: 24px;
                white-space: pre-line;
            }
            
            .custom-modal-buttons {
                display: flex;
                gap: 12px;
            }
            
            .custom-btn {
                flex: 1;
                padding: 14px 20px;
                border: none;
                border-radius: 12px;
                font-size: 15px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s ease;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .custom-btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            }
            
            .custom-btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 35px rgba(102, 126, 234, 0.6);
            }
            
            .custom-btn-secondary {
                background: #f1f5f9;
                color: #64748b;
            }
            
            .custom-btn-secondary:hover {
                background: #e2e8f0;
            }
            
            .custom-btn-danger {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                color: white;
                box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
            }
            
            .custom-btn-danger:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 35px rgba(239, 68, 68, 0.6);
            }
            
            .custom-btn-success {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white;
                box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            }
            
            .custom-btn-success:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 35px rgba(16, 185, 129, 0.6);
            }
        `;
        document.head.appendChild(style);
    }
    
    // Close when clicking outside
    document.getElementById('customAlertModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCustomAlert();
        }
    });
}

// Show custom alert
function customAlert(message, title = 'Notification', icon = '✓', type = 'success') {
    initCustomAlert();
    
    const modal = document.getElementById('customAlertModal');
    const modalIcon = document.getElementById('customModalIcon');
    const modalTitle = document.getElementById('customModalTitle');
    const modalMessage = document.getElementById('customModalMessage');
    const modalButtons = document.getElementById('customModalButtons');
    
    // Set icon and type
    modalIcon.textContent = icon;
    modalIcon.className = 'custom-modal-icon ' + type;
    
    // Set content
    modalTitle.textContent = title;
    modalMessage.textContent = message;
    
    // Set buttons (only OK for alert)
    modalButtons.innerHTML = '<button type="button" class="custom-btn custom-btn-primary" onclick="closeCustomAlert()">OK</button>';
    
    // Show modal
    modal.classList.add('show');
}

// Show custom confirm
function customConfirm(message, title = 'Confirmation', onConfirm, onCancel = null, confirmText = 'OK', cancelText = 'Cancel') {
    initCustomAlert();
    
    const modal = document.getElementById('customAlertModal');
    const modalIcon = document.getElementById('customModalIcon');
    const modalTitle = document.getElementById('customModalTitle');
    const modalMessage = document.getElementById('customModalMessage');
    const modalButtons = document.getElementById('customModalButtons');
    
    // Set icon and type
    modalIcon.textContent = '❓';
    modalIcon.className = 'custom-modal-icon warning';
    
    // Set content
    modalTitle.textContent = title;
    modalMessage.textContent = message;
    
    // Set buttons (OK and Cancel for confirm)
    modalButtons.innerHTML = `
        <button type="button" class="custom-btn custom-btn-secondary" onclick="handleCustomCancel()">${cancelText}</button>
        <button type="button" class="custom-btn custom-btn-primary" onclick="handleCustomConfirm()">${confirmText}</button>
    `;
    
    // Store callbacks
    window._customConfirmCallback = onConfirm;
    window._customCancelCallback = onCancel;
    
    // Show modal
    modal.classList.add('show');
}

// Handle confirm
function handleCustomConfirm() {
    closeCustomAlert();
    if (window._customConfirmCallback) {
        window._customConfirmCallback();
    }
}

// Handle cancel
function handleCustomCancel() {
    closeCustomAlert();
    if (window._customCancelCallback) {
        window._customCancelCallback();
    }
}

// Close custom alert
function closeCustomAlert() {
    const modal = document.getElementById('customAlertModal');
    if (modal) {
        modal.classList.remove('show');
    }
}

// Override native alert (optional)
// window.alert = function(message) {
//     customAlert(message, 'Alert', '⚠️', 'warning');
// };
