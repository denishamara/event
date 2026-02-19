<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $user;

    public function __construct()
    {
        $this->user = new UserModel();
    }

    public function login()
    {
        return view('auth/login');
    }

    public function loginProcess()
    {
        $email    = $this->request->getPost('email');
        $password = md5($this->request->getPost('password'));

        $user = $this->user
            ->where('email', $email)
            ->where('password', $password)
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email atau password salah');
        }

        session()->set([
            'user_id' => $user['id'],
            'name'    => $user['name'],
            'role'    => $user['role'],
            'isLogin' => true
        ]);

        return redirect()->to('/dashboard');
    }

    /* ================= REGISTER ================= */

    public function register()
    {
        return view('auth/register');
    }

    public function registerProcess()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Validasi konfirmasi password
        if ($password !== $confirmPassword) {
            return redirect()->back()->with('error', 'Password dan konfirmasi password tidak cocok');
        }

        // Validasi panjang password
        if (strlen($password) < 6) {
            return redirect()->back()->with('error', 'Password minimal 6 karakter');
        }

        // cek email sudah ada
        $exists = $this->user->where('email', $email)->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Email sudah terdaftar');
        }

        // insert user baru
        $this->user->insert([
            'name'     => $this->request->getPost('name'),
            'email'    => $email,
            'password' => md5($password),
            'role'     => 'user'
        ]);

        // auto login
        $user = $this->user->where('email', $email)->first();
        session()->set([
            'user_id' => $user['id'],
            'name'    => $user['name'],
            'role'    => $user['role'],
            'isLogin' => true
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    /* ================= FORGOT PASSWORD ================= */

    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    public function forgotPasswordProcess()
    {
        $email = $this->request->getPost('email');
        
        // Check if email exists
        $user = $this->user->where('email', $email)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak ditemukan');
        }
        
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Update user with reset token
        $this->user->update($user['id'], [
            'reset_token' => $token,
            'reset_token_expires' => $expires
        ]);
        
        // Send email with reset link
        $resetLink = base_url("/reset-password?token=$token");
        
        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('Reset Password - Event App');
        
        $message = "
            <h2>Reset Password Request</h2>
            <p>Hi {$user['name']},</p>
            <p>Anda menerima email ini karena ada permintaan untuk reset password akun Anda.</p>
            <p>Klik link berikut untuk reset password:</p>
            <p><a href='$resetLink' style='display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 8px;'>Reset Password</a></p>
            <p>Atau copy link berikut ke browser Anda:</p>
            <p>$resetLink</p>
            <p>Link ini akan expired dalam 1 jam.</p>
            <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
            <br>
            <p>Terima kasih,<br>Event App Team</p>
        ";
        
        $emailService->setMessage($message);
        
        if ($emailService->send()) {
            return redirect()->back()->with('success', 'Link reset password telah dikirim ke email Anda. Silakan cek email Anda.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengirim email. Silakan coba lagi.');
        }
    }

    public function resetPassword()
    {
        $token = $this->request->getGet('token');
        
        if (!$token) {
            return redirect()->to('/login')->with('error', 'Token tidak valid');
        }
        
        // Check if token exists and not expired
        $user = $this->user->where('reset_token', $token)->first();
        
        if (!$user) {
            return redirect()->to('/login')->with('error', 'Token tidak valid');
        }
        
        if (strtotime($user['reset_token_expires']) < time()) {
            return redirect()->to('/login')->with('error', 'Token sudah expired. Silakan request reset password lagi.');
        }
        
        return view('auth/reset_password', ['token' => $token]);
    }

    public function resetPasswordProcess()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');
        
        // Validate password match
        if ($password !== $passwordConfirm) {
            return redirect()->back()->with('error', 'Password dan konfirmasi password tidak cocok');
        }
        
        // Validate password length
        if (strlen($password) < 6) {
            return redirect()->back()->with('error', 'Password minimal 6 karakter');
        }
        
        // Check token
        $user = $this->user->where('reset_token', $token)->first();
        
        if (!$user) {
            return redirect()->to('/login')->with('error', 'Token tidak valid');
        }
        
        if (strtotime($user['reset_token_expires']) < time()) {
            return redirect()->to('/login')->with('error', 'Token sudah expired. Silakan request reset password lagi.');
        }
        
        // Update password and clear reset token
        $this->user->update($user['id'], [
            'password' => md5($password),
            'reset_token' => null,
            'reset_token_expires' => null
        ]);
        
        return redirect()->to('/login')->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }
}
