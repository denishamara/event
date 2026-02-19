<!DOCTYPE html>
<html>
<head>
    <title>Login - Event App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        
        .login-box {
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
        
        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }
        
        .login-icon {
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
        
        .login-box h2 {
            color: #1e293b;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 6px;
        }
        
        .subtitle {
            color: #64748b;
            font-size: 14px;
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
        
        .toggle-password {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            cursor: pointer;
            transition: color 0.3s;
            z-index: 10;
        }
        
        .toggle-password:hover {
            color: #667eea;
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
        
        .divider {
            text-align: center;
            margin: 20px 0;
            color: #94a3b8;
            font-size: 12px;
            position: relative;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 42%;
            height: 2px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
        }
        
        .divider::before { left: 0; }
        .divider::after { right: 0; }
        
        .register-link {
            display: block;
            text-align: center;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .register-link strong {
            color: #667eea;
            font-weight: 700;
            transition: all 0.3s;
        }
        
        .register-link:hover strong {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .forgot-password-link {
            display: block;
            text-align: right;
            color: #667eea;
            text-decoration: none;
            font-size: 13px;
            margin-top: 8px;
            margin-bottom: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .forgot-password-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .login-box {
                padding: 40px 30px;
            }
            
            .login-icon {
                width: 70px;
                height: 70px;
                font-size: 32px;
            }
        }
    </style>
</head>
<body>

<div class="login-box">
    <div class="login-header">
        <div class="login-icon">🎫</div>
        <h2>Welcome Back!</h2>
        <p class="subtitle">Sign in to continue to Event App</p>
    </div>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="error-message">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>
    
    <form method="post" action="/login">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Email Address</label>
            <div class="input-wrapper">
                <input type="email" name="email" placeholder="Enter your email" required>
                <i class="input-icon fas fa-envelope"></i>
            </div>
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <div class="input-wrapper">
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <i class="input-icon fas fa-lock"></i>
                <i class="toggle-password fas fa-eye" onclick="togglePassword('password')"></i>
            </div>
            <a href="/forgot-password" class="forgot-password-link">Forgot Password?</a>
        </div>
        
        <button type="submit" class="btn">Login</button>
    </form>
    
    <div class="divider">or</div>
    
    <a href="/register" class="register-link">
        Don't have an account? <strong>Register now</strong>
    </a>
</div>

<script>
function togglePassword(fieldId) {
    const input = document.getElementById(fieldId);
    const icon = input.parentElement.querySelector('.toggle-password');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>
