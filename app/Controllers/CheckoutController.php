<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\EventPriceModel;
use App\Models\EventTicketModel;
use App\Models\PaymentModel;

class CheckoutController extends BaseController
{
    protected $event;
    protected $price;
    protected $ticket;
    protected $payment;
    protected $db;

    public function __construct()
    {
        $this->event   = new EventModel();
        $this->price   = new EventPriceModel();
        $this->ticket  = new EventTicketModel();
        $this->payment = new PaymentModel();
        $this->db      = \Config\Database::connect();
    }

    public function index($eventId)
    {
        // Auto-rollover before displaying
        $rolloverService = new \App\Libraries\PhaseRolloverService();
        $rolloverService->rolloverEvent($eventId);
        
        $event = $this->event->find($eventId);

        $prices = $this->price
            ->where('event_id', $eventId)
            ->orderBy('order_index','ASC')
            ->findAll();

        // Hitung hari sampai event (sama seperti AdminEventController)
        $eventDate = new \DateTime(date('Y-m-d', strtotime($event['event_date'])));
        $today = new \DateTime(date('Y-m-d'));
        $daysUntilEvent = (int) $today->diff($eventDate)->format('%r%a');
        
        if ($daysUntilEvent < 0) {
            $daysUntilEvent = 0;
        }

        // REGULAR EVENT
        if (count($prices) === 0) {
            $prices[] = [
                'id' => 0,
                'phase' => 'Regular',
                'price' => $event['price'],
                'quota_remaining' => $event['quota'],
                'is_active' => true
            ];

            $event['display_price'] = $event['price'];
            $event['price_phase']   = 'Regular';
        } else {
            // Filter hanya phase yang aktif berdasarkan hari (sama seperti AdminEventController)
            $activePhase = null;
            $nextPhase = null;
            
            foreach ($prices as &$p) {
                $p['is_active'] = false;
                
                // Phase aktif jika: end_day <= daysUntilEvent <= start_day
                // Contoh: start_day=30, end_day=20 -> aktif di hari 20-30 sebelum event
                $isInTimeRange = $daysUntilEvent >= (int)$p['end_day'] && $daysUntilEvent <= (int)$p['start_day'];
                
                if ($isInTimeRange) {
                    $p['is_active'] = true;
                    if ((int)$p['quota_remaining'] > 0) {
                        $activePhase = $p;
                    }
                }
                
                // Cari next phase yang akan aktif (hari lebih besar dari start_day)
                if ($daysUntilEvent > (int)$p['start_day'] && $nextPhase === null) {
                    $nextPhase = $p;
                }
            }
            
            if ($activePhase) {
                $event['display_price'] = $activePhase['price'];
                $event['price_phase']   = $activePhase['phase'];
                $event['active_phase_has_quota'] = true;
            } else {
                // Tidak ada phase aktif dengan kuota
                $event['display_price'] = null;
                $event['price_phase']   = null;
                $event['active_phase_has_quota'] = false;
            }
            
            $event['next_phase'] = $nextPhase;
            $event['days_until_event'] = $daysUntilEvent;
        }

        return view('checkout/index', [
            'event' => $event,
            'pricePhases' => $prices
        ]);
    }

    public function process()
    {
        $eventId = $this->request->getPost('event_id');
        $qty     = (int) $this->request->getPost('qty');
        $method  = $this->request->getPost('payment_method');

        $event = $this->event->find($eventId);

        $prices = $this->price
            ->where('event_id', $eventId)
            ->orderBy('order_index', 'ASC')
            ->findAll();

        // Hitung hari sampai event (sama seperti AdminEventController)
        $eventDate = new \DateTime(date('Y-m-d', strtotime($event['event_date'])));
        $today = new \DateTime(date('Y-m-d'));
        $daysUntilEvent = (int) $today->diff($eventDate)->format('%r%a');
        
        if ($daysUntilEvent < 0) {
            $daysUntilEvent = 0;
        }

        // Tentukan phase dan harga
        $phase = 'Regular';
        $pricePerTicket = $event['price'];

        if (count($prices) > 0) {
            // Filter hanya phase yang aktif
            $activePhase = null;
            
            foreach ($prices as $p) {
                $isInTimeRange = $daysUntilEvent >= (int)$p['end_day'] && $daysUntilEvent <= (int)$p['start_day'];
                
                if ($isInTimeRange && (int)$p['quota_remaining'] > 0) {
                    $activePhase = $p;
                    break;
                }
            }
            
            if (!$activePhase) {
                return redirect()->back()->with('error', 'Tidak ada phase aktif dengan kuota tersedia.');
            }
            
            if ($qty > $activePhase['quota_remaining']) {
                return redirect()->back()->with('error', 'Jumlah tiket melebihi kuota phase aktif.');
            }

            $phase = $activePhase['phase'];
            $pricePerTicket = $activePhase['price'];
        }

        $total = $qty * $pricePerTicket;

        // Simpan ke session untuk billing
        session()->set('pending_order', [
            'event_id' => $eventId,
            'event_title' => $event['title'],
            'event_location' => $event['location'],
            'event_date' => $event['event_date'],
            'qty' => $qty,
            'phase' => $phase,
            'price_per_ticket' => $pricePerTicket,
            'total' => $total,
            'payment_method' => $method,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/checkout/billing');
    }

    public function billing()
    {
        $orderData = session()->get('pending_order');
        
        if (!$orderData) {
            return redirect()->to('/events')->with('error', 'No pending order found');
        }

        // Generate billing number
        $billingNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        $orderData['order_id'] = $billingNumber;

        // Generate QR Code untuk pembayaran
        $qrData = "PAYMENT:{$orderData['payment_method']}|EVENT:{$orderData['event_id']}|TOTAL:{$orderData['total']}|INV:{$billingNumber}";
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);

        return view('checkout/billing', [
            'orderData' => $orderData,
            'billingNumber' => $billingNumber,
            'qrUrl' => $qrUrl
        ]);
    }

    public function submitPayment()
    {
        $orderId = $this->request->getPost('order_id');
        $proof = $this->request->getFile('payment_proof');

        $orderData = session()->get('pending_order');
        
        if (!$orderData) {
            return redirect()->to('/events')->with('error', 'Order session expired');
        }

        // Validasi file
        if (!$proof->isValid()) {
            return redirect()->back()->with('error', 'Invalid payment proof file');
        }

        $this->db->transStart();

        $prices = $this->price
            ->where('event_id', $orderData['event_id'])
            ->orderBy('order_index', 'ASC')
            ->findAll();

        // Hitung hari sampai event untuk validasi ulang
        $event = $this->event->find($orderData['event_id']);
        $eventDate = new \DateTime(date('Y-m-d', strtotime($event['event_date'])));
        $today = new \DateTime(date('Y-m-d'));
        $daysUntilEvent = (int) $today->diff($eventDate)->format('%r%a');
        
        if ($daysUntilEvent < 0) {
            $daysUntilEvent = 0;
        }

        // Create 1 ticket grup dengan qty (bukan loop buat banyak tiket)
        $ticketData = [
            'ticket_code' => 'EVT-' . strtoupper(uniqid()),
            'event_id' => $orderData['event_id'],
            'user_id' => session()->get('user_id'),
            'phase' => 'Regular',
            'price' => $orderData['price_per_ticket'],
            'qty' => $orderData['qty'],
            'status' => 'pending'
        ];

        // Tentukan phase berdasarkan prices
        if (count($prices) > 0) {
            // Phase event - cari active phase
            $activePhase = null;
            foreach ($prices as $p) {
                $isInTimeRange = $daysUntilEvent >= (int)$p['end_day'] && $daysUntilEvent <= (int)$p['start_day'];
                if ($isInTimeRange && (int)$p['quota_remaining'] > 0) {
                    $activePhase = $p;
                    break;
                }
            }

            if (!$activePhase || $orderData['qty'] > $activePhase['quota_remaining']) {
                $this->db->transRollback();
                session()->remove('pending_order');
                return redirect()->to('/events')->with('error', 'Phase sudah tidak aktif atau kuota tidak mencukupi');
            }

            // Update ticket data dengan phase
            $ticketData['phase'] = $activePhase['phase'];
            $ticketData['price'] = $activePhase['price'];
            
            // CATATAN: Quota TIDAK dipotong di sini
            // Quota akan dipotong saat admin approve payment
            // Jika quota habis, admin bisa reject payment
        }

        // Insert 1 tiket grup
        $ticketId = $this->ticket->insert($ticketData, true);

        // Upload proof
        $proofName = $proof->getRandomName();
        $proof->move(WRITEPATH . 'uploads/proofs', $proofName);

        // Create payment
        $paymentId = $this->payment->insert([
            'event_ticket_id' => $ticketId,
            'method' => $orderData['payment_method'],
            'amount' => $orderData['total'],
            'qty' => $orderData['qty'],
            'status' => 'pending',
            'proof_file' => $proofName
        ], true);

        // Link ticket to payment
        $this->ticket->update($ticketId, [
            'payment_id' => $paymentId
        ]);

        $this->db->transComplete();

        // Clear session
        session()->remove('pending_order');

        return redirect()->to('/my-tickets')
            ->with('success', 'Pembayaran berhasil dikirim! Menunggu verifikasi admin.');
    }
}
