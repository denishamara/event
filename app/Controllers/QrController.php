<?php

namespace App\Controllers;

use App\Models\EventTicketModel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrController extends BaseController
{
    public function generate($ticketId)
    {
        $ticketModel = new EventTicketModel();
        $ticket = $ticketModel->find($ticketId);

        // Validate ticket exists and belongs to logged-in user (or is admin)
        if (!$ticket) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Ticket not found');
        }

        // Security: Check ownership
        $userId = session()->get('id');
        $role = session()->get('role');
        
        if ($role !== 'admin' && $ticket['user_id'] != $userId) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }

        // Check if QR data exists
        if (empty($ticket['qr_data'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('QR code not generated yet');
        }

        try {
            // Generate QR Code
            $qrCode = QrCode::create($ticket['qr_data'])
                ->setSize(300)
                ->setMargin(10);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Return QR code as PNG image
            return $this->response
                ->setHeader('Content-Type', 'image/png')
                ->setHeader('Cache-Control', 'public, max-age=3600')
                ->setBody($result->getString());
                
        } catch (\Exception $e) {
            // Fallback: Generate simple error image
            log_message('error', 'QR Code generation failed: ' . $e->getMessage());
            
            // Create simple error image
            $img = imagecreate(300, 300);
            $bg = imagecolorallocate($img, 255, 255, 255);
            $text = imagecolorallocate($img, 255, 0, 0);
            imagestring($img, 5, 50, 140, 'QR Error', $text);
            
            ob_start();
            imagepng($img);
            $imgData = ob_get_clean();
            imagedestroy($img);
            
            return $this->response
                ->setHeader('Content-Type', 'image/png')
                ->setBody($imgData);
        }
    }

    public function verify($qrData = null)
    {
        // Admin only
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $ticketModel = new EventTicketModel();
        $db = \Config\Database::connect();

        $result = null;
        $ticket = null;

        // Get qr_data from URL parameter or GET parameter
        if (!$qrData) {
            $qrData = $this->request->getGet('qr_data');
        }

        if ($qrData) {
            // Trim whitespace
            $qrData = trim($qrData);
            
            // Search ticket by QR data
            $ticket = $db->table('event_tickets')
                ->select('event_tickets.*, events.title AS event_title, events.event_date, users.name AS user_name')
                ->join('events', 'events.id = event_tickets.event_id')
                ->join('users', 'users.id = event_tickets.user_id')
                ->where('event_tickets.qr_data', $qrData)
                ->get()
                ->getRowArray();

            if (!$ticket) {
                $result = [
                    'status' => 'invalid',
                    'message' => '❌ Invalid QR Code - Ticket not found',
                    'debug' => 'QR Data length: ' . strlen($qrData) . ' characters'
                ];
            } elseif ($ticket['status'] !== 'paid') {
                $result = [
                    'status' => 'invalid',
                    'message' => '❌ Ticket not active (Status: ' . $ticket['status'] . ')',
                    'ticket' => $ticket
                ];
            } elseif ($ticket['is_checked_in']) {
                $result = [
                    'status' => 'already_used',
                    'message' => '⚠️ Already checked in at: ' . date('d M Y H:i', strtotime($ticket['checked_in_at'])),
                    'ticket' => $ticket
                ];
            } else {
                $result = [
                    'status' => 'valid',
                    'message' => '✅ Valid Ticket - Ready to check in',
                    'ticket' => $ticket
                ];
            }
        }

        return view('admin/qr/verify', [
            'result' => $result,
            'ticket' => $ticket,
            'qrData' => $qrData
        ]);
    }

    public function checkin()
    {
        // Admin only
        if (session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $qrData = $this->request->getPost('qr_data');

        if (!$qrData) {
            return $this->response->setJSON(['success' => false, 'message' => 'QR data required']);
        }

        $ticketModel = new EventTicketModel();
        $ticket = $ticketModel->where('qr_data', $qrData)->first();

        if (!$ticket) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ticket not found']);
        }

        if ($ticket['status'] !== 'paid') {
            return $this->response->setJSON(['success' => false, 'message' => 'Ticket not active']);
        }

        if ($ticket['is_checked_in']) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Already checked in at ' . date('d M Y H:i', strtotime($ticket['checked_in_at']))
            ]);
        }

        // Check in ticket
        $ticketModel->update($ticket['id'], [
            'is_checked_in' => 1,
            'checked_in_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true, 
            'message' => 'Check-in successful!',
            'ticket' => $ticket
        ]);
    }
}
