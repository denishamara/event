<?php

namespace App\Controllers;

class TestEmailController extends BaseController
{
    public function index()
    {
        $email = \Config\Services::email();

        $email->setTo('denishamara07@gmail.com');
        $email->setSubject('SMTP TEST - CI4 Event App');
        $email->setMessage('
            <h2>✅ Email berhasil</h2>
            <p>SMTP Gmail sudah aktif dan siap dipakai.</p>
        ');

        if (!$email->send()) {
            return $email->printDebugger(['headers']);
        }

        return 'EMAIL BERHASIL TERKIRIM 🚀';
    }
}
