<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - Event App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="/assets/js/custom-alert.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -250px;
            right: -250px;
            animation: float 20s ease-in-out infinite;
        }
        
        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            bottom: -200px;
            left: -200px;
            animation: float 15s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-50px) rotate(180deg); }
        }
        
        .reset-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.3);
            padding: 35px 40px;
            width: 100%;
            max-width: 460px;
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 28px;
        }
        
        .reset-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            animation: iconPulse 2s ease-in-out infinite;
        }
        
        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .reset-box h2 {
            color: #1e293b;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 6px;
        }
        
        .subtitle {
            color: #64748b;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .error-message {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #dc2626;
            padding: 14px 18px;
            border-radius: 14px;
            margin-bottom: 24px;
            font-size: 14px;
            border-left: 4px solid #dc2626;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s;
        }
        
        .error-message::before {
            content: '⚠️';
            font-size: 18px;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .form-group {
            margin-bottom: 16px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            transition: color 0.3s;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 18px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8fafc;
            color: #1e293b;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 5px rgba(102, 126, 234, 0.1);
        }
        
        .form-group input:focus + .input-icon {
            color: #667eea;
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 6px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn:hover::before {
            width: 400px;
            height: 400px;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.6);
        }
        
        .btn:active {
            transform: translateY(-1px);
        }
        
        .back-link {
            display: block;
            text-align: center;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            margin-top: 20px;
            transition: color 0.3s;
        }
        
        .back-link strong {
            color: #667eea;
            font-weight: 700;
            transition: all 0.3s;
        }
        
        .back-link:hover strong {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .password-requirements {
            background: #f1f5f9;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            color: #475569;
            margin-bottom: 16px;
        }
        
        .password-requirements ul {
            margin: 8px 0 0 20px;
        }
        
        .password-requirements li {
            margin: 4px 0;
        }
        
        @media (max-width: 480px) {
            .reset-box {
                padding: 40px 30px;
            }
            
            .reset-icon {
                width: 70px;
                height: 70px;
                font-size: 32px;
            }
        }
    </style>
</head>
<body>

<div class="reset-box">
    <div class="reset-header">
        <div class="reset-icon">🔐</div>
        <h2>Reset Password</h2>
        <p class="subtitle">Enter your new password below.</p>
    </div>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="error-message">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>
    
    <form method="post" action="/reset-password">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= esc($token) ?>">
        
        <div class="form-group">
            <label>New Password</label>
            <div class="input-wrapper">
                <input type="password" name="password" id="password" placeholder="Enter new password" required minlength="6">
                <i class="input-icon fas fa-lock"></i>
            </div>
        </div>
        
        <div class="form-group">
            <label>Confirm Password</label>
            <div class="input-wrapper">
                <input type="password" name="password_confirm" id="password_confirm" placeholder="Confirm new password" required minlength="6">
                <i class="input-icon fas fa-lock"></i>
            </div>
        </div>
        
        <button type="submit" class="btn">Reset Password</button>
    </form>
    
    <a href="/login" class="back-link">
        <strong>← Back to Login</strong>
    </a>
</div>

<script>
    // Password confirmation validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirm').value;
        
        if (password !== confirm) {
            e.preventDefault();
            customAlert(
                'Password dan konfirmasi password yang Anda masukkan tidak cocok. Silakan periksa kembali.',
                'Password Tidak Cocok!',
                '⚠️',
                'error'
            );
        }
    });
</script>

</body>
</html>
