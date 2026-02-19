<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\EventPriceModel;

class AdminEventController extends BaseController
{
    protected $event;
    protected $price;

    public function __construct()
    {
        $this->event = new EventModel();
        $this->price = new EventPriceModel();
    }

    private function onlyAdmin()
    {
        if (!session()->get('isLogin') || session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->send();
        }
    }

    /* ======================
        EVENT LIST
    ====================== */
    public function index()
    {
        $this->onlyAdmin();

        // Order by event_date ASC - event yang paling dekat di atas
        $events = $this->event->orderBy('event_date','ASC')->findAll();
        
        // Tambahkan info fase harga aktif untuk setiap event
        foreach ($events as &$event) {
            $eventDate = new \DateTime(date('Y-m-d', strtotime($event['event_date'])));
            $today = new \DateTime(date('Y-m-d'));
            $diffDays = (int) $today->diff($eventDate)->format('%r%a');
            if ($diffDays < 0) $diffDays = 0;
            
            // Ambil semua fase untuk event ini (sudah terurut by order_index ASC)
            $prices = $this->price
                ->where('event_id', $event['id'])
                ->orderBy('order_index', 'ASC')
                ->findAll();
            
            $activePrice = null;
            
            // Cari fase yang aktif berdasarkan WAKTU dan QUOTA
            foreach ($prices as $p) {
                // Untuk phase yang pakai quota
                if ((int)$p['use_quota'] === 1) {
                    // Cek apakah masih dalam rentang waktu phase ini
                    $isInTimeRange = $diffDays >= (int)$p['end_day'] && $diffDays <= (int)$p['start_day'];
                    
                    // Phase aktif jika: masih ada quota DAN masih dalam rentang waktu
                    if ((int)$p['quota_remaining'] > 0 && $isInTimeRange) {
                        $activePrice = $p;
                        break;
                    }
                    
                    // Jika quota habis ATAU waktu sudah lewat, lanjut ke phase berikutnya
                    continue;
                }
                
                // Untuk phase tanpa quota (cek waktu saja)
                if ($diffDays >= (int)$p['end_day'] && $diffDays <= (int)$p['start_day']) {
                    $activePrice = $p;
                    break;
                }
            }
            
            if ($activePrice) {
                $event['price_phase'] = $activePrice['phase'];
                $event['display_price'] = $activePrice['price'];
            } else {
                $event['price_phase'] = 'Regular';
                $event['display_price'] = $event['price'];
            }
        }

        return view('admin/events/index', [
            'events' => $events
        ]);
    }

    /* ======================
    EDIT EVENT
====================== */
public function edit($id)
{
    $this->onlyAdmin();

    $event = $this->event->find($id);
    if (!$event) {
        return redirect()->to('/admin/events');
    }

    $prices = $this->price
        ->where('event_id', $id)
        ->orderBy('order_index', 'ASC')
        ->findAll();

    $priceMap = [
        'early' => null,
        'presale' => null,
        'last' => null
    ];

    foreach ($prices as $p) {
        if ($p['phase'] === 'Early Bird') {
            $priceMap['early'] = $p;
        }
        if ($p['phase'] === 'Presale') {
            $priceMap['presale'] = $p;
        }
        if ($p['phase'] === 'Last Minute') {
            $priceMap['last'] = $p;
        }
    }

    // Jika event reguler lama belum punya pricing phase, buat default
    if (empty($prices)) {
        $eventPrice = $event['price'];
        $eventQuota = $event['quota'];
        
        // JANGAN buat default value - biarkan kosong untuk event reguler
        $priceMap['early'] = [
            'price' => '',
            'start_day' => '',
            'end_day' => '',
            'quota_total' => ''
        ];
        
        $priceMap['presale'] = [
            'price' => '',
            'start_day' => '',
            'end_day' => '',
            'quota_total' => ''
        ];
        
        $priceMap['last'] = [
            'price' => '',
            'start_day' => '',
            'end_day' => '',
            'quota_total' => ''
        ];
    }

    return view('admin/events/edit', [
        'event' => $event,
        'priceMap' => $priceMap
    ]);
}

/* ======================
    UPDATE EVENT
====================== */
public function update($id)
{
    $this->onlyAdmin();

    $event = $this->event->find($id);
    if (!$event) {
        return redirect()->to('/admin/events')
            ->with('error', 'Event tidak ditemukan');
    }

    // Validasi input
    $eventType = $this->request->getPost('event_type'); // 'regular' atau 'phase'
    
    $rules = [
        'title' => 'required',
        'description' => 'required',
        'location' => 'required',
        'event_date' => 'required',
        'price' => 'required|numeric',
        'quota' => 'required|numeric',
    ];

    // Jika event dengan phase, tambahkan validasi phase
    if ($eventType === 'phase') {
        $rules['price_early'] = 'required|numeric';
        $rules['price_presale'] = 'required|numeric';
        $rules['price_last'] = 'required|numeric';
        $rules['early_quota'] = 'required|numeric';
        $rules['presale_quota'] = 'required|numeric';
    }

    if (!$this->validate($rules)) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Mohon lengkapi semua field dengan benar');
    }

    $totalQuota = (int)$this->request->getPost('quota');

    // Validasi quota hanya untuk event dengan phase
    if ($eventType === 'phase') {
        $earlyQuota   = (int)$this->request->getPost('early_quota');
        $presaleQuota = (int)$this->request->getPost('presale_quota');

        if (($earlyQuota + $presaleQuota) > $totalQuota) {
            return redirect()->back()
                ->withInput()
                ->with('error','Total kuota Early Bird + Presale melebihi quota event');
        }
    }
    $image = $this->request->getFile('image');

if ($image && $image->isValid() && !$image->hasMoved()) {

    $newName = $image->getRandomName();
    $image->move('uploads/events', $newName);

    // hapus gambar lama
    if (!empty($event['image'])) {
        $oldPath = 'uploads/events/' . $event['image'];
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }

    $this->event->update($id, [
        'image' => $newName
    ]);
    }
    // UPDATE EVENT
    $this->event->update($id, [
        'title' => $this->request->getPost('title'),
        'description' => $this->request->getPost('description'),
        'location' => $this->request->getPost('location'),
        'event_date' => $this->request->getPost('event_date'),
        'price' => $this->request->getPost('price'),
        'quota' => $totalQuota
    ]);

    // HAPUS PRICE LAMA
    $this->price->where('event_id', $id)->delete();

    // Jika event dengan phase, buat pricing phases
    if ($eventType === 'phase') {
        $order = 1;
        $earlyQuota   = (int)$this->request->getPost('early_quota');
        $presaleQuota = (int)$this->request->getPost('presale_quota');

        // EARLY
        $this->price->insert([
            'event_id' => $id,
            'phase' => 'Early Bird',
            'order_index' => $order++,
            'price' => $this->request->getPost('price_early'),
            'start_day' => $this->request->getPost('early_start'),
            'end_day' => $this->request->getPost('early_end'),
            'use_quota' => 1,
            'quota_total' => $earlyQuota,
            'quota_remaining' => $earlyQuota
        ]);

        // PRESALE
        $this->price->insert([
            'event_id' => $id,
            'phase' => 'Presale',
            'order_index' => $order++,
            'price' => $this->request->getPost('price_presale'),
            'start_day' => $this->request->getPost('presale_start'),
            'end_day' => $this->request->getPost('presale_end'),
            'use_quota' => 1,
            'quota_total' => $presaleQuota,
            'quota_remaining' => $presaleQuota
        ]);

        // LAST MINUTE
        $lastQuota = $totalQuota - ($earlyQuota + $presaleQuota);

        $this->price->insert([
            'event_id' => $id,
            'phase' => 'Last Minute',
            'order_index' => $order++,
            'price' => $this->request->getPost('price_last'),
            'start_day' => $this->request->getPost('last_start'),
            'end_day' => $this->request->getPost('last_end'),
            'use_quota' => 1,
            'quota_total' => $lastQuota,
            'quota_remaining' => $lastQuota
        ]);
    }
    // Jika event reguler, tidak ada pricing phase yang disimpan

    return redirect()->to('/admin/events')
        ->with('success','Event updated successfully');
}

    /* ======================
        CREATE
    ====================== */
    public function create()
    {
        $this->onlyAdmin();
        return view('admin/events/create');
    }

    /* ======================
        STORE
    ====================== */
    public function store()
{
    $this->onlyAdmin();

    $totalQuota = (int)$this->request->getPost('quota');

    /* ===============================
       UPLOAD IMAGE
    =============================== */
    $imageName = null;
    $image = $this->request->getFile('image');

    if ($image && $image->isValid() && !$image->hasMoved()) {
        $imageName = $image->getRandomName();
        $image->move('uploads/events', $imageName);
    }

    /* ===============================
       INSERT EVENT
    =============================== */
    $eventId = $this->event->insert([
        'title'       => $this->request->getPost('title'),
        'description' => $this->request->getPost('description'),
        'location'    => $this->request->getPost('location'),
        'event_date'  => $this->request->getPost('event_date'),
        'price'       => $this->request->getPost('price'),
        'quota'       => $totalQuota,
        'image'       => $imageName
    ], true);

    /* ===============================
       CEK TIPE EVENT
    =============================== */

    $eventType = $this->request->getPost('event_type'); // 'regular' atau 'phase'

    if ($eventType === 'regular') {
        // 🔥 REGULAR EVENT — STOP DI SINI
        return redirect()->to('/admin/events')
            ->with('success', 'Regular event created successfully');
    }

    /* ===============================
       INSERT PHASE (ONLY FOR PHASE EVENT)
    =============================== */

    $order = 1;

    // EARLY
    if ($this->request->getPost('price_early')) {
        $quota = (int)$this->request->getPost('early_quota');

        $this->price->insert([
            'event_id' => $eventId,
            'phase' => 'Early Bird',
            'order_index' => $order++,
            'price' => $this->request->getPost('price_early'),
            'start_day' => $this->request->getPost('early_start'),
            'end_day' => $this->request->getPost('early_end'),
            'use_quota' => 1,
            'quota_total' => $quota,
            'quota_remaining' => $quota
        ]);
    }

    // PRESALE
    if ($this->request->getPost('price_presale')) {
        $quota = (int)$this->request->getPost('presale_quota');

        $this->price->insert([
            'event_id' => $eventId,
            'phase' => 'Presale',
            'order_index' => $order++,
            'price' => $this->request->getPost('price_presale'),
            'start_day' => $this->request->getPost('presale_start'),
            'end_day' => $this->request->getPost('presale_end'),
            'use_quota' => 1,
            'quota_total' => $quota,
            'quota_remaining' => $quota
        ]);
    }

    // LAST MINUTE
    if ($this->request->getPost('price_last')) {
        $used = 
            (int)$this->request->getPost('early_quota') +
            (int)$this->request->getPost('presale_quota');

        $lastQuota = $totalQuota - $used;

        $this->price->insert([
            'event_id' => $eventId,
            'phase' => 'Last Minute',
            'order_index' => $order++,
            'price' => $this->request->getPost('price_last'),
            'start_day' => $this->request->getPost('last_start'),
            'end_day' => $this->request->getPost('last_end'),
            'use_quota' => 1,
            'quota_total' => $lastQuota,
            'quota_remaining' => $lastQuota
        ]);
    }

    return redirect()->to('/admin/events')
        ->with('success','Event created successfully');
}

    /* ======================
        DELETE EVENT
    ====================== */
    public function delete($id)
    {
        $this->onlyAdmin();

        $event = $this->event->find($id);
        if (!$event) {
            return redirect()->to('/admin/events')
                ->with('error', 'Event not found');
        }

        // Load models yang diperlukan
        $ticketModel = new \App\Models\EventTicketModel();
        $paymentModel = new \App\Models\PaymentModel();
        $refundModel = new \App\Models\RefundModel();

        // Ambil semua tickets untuk event ini
        $tickets = $ticketModel->where('event_id', $id)->findAll();

        // Untuk setiap ticket, hapus data terkait
        foreach ($tickets as $ticket) {
            // Hapus refunds jika ada
            $refundModel->where('ticket_id', $ticket['id'])->delete();
            
            // Hapus payments yang terkait dengan ticket ini
            $paymentModel->where('event_ticket_id', $ticket['id'])->delete();
        }

        // Hapus semua tickets event ini
        $ticketModel->where('event_id', $id)->delete();

        // Hapus gambar jika ada
        if (!empty($event['image'])) {
            $imagePath = 'uploads/events/' . $event['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Hapus semua price phases terkait
        $this->price->where('event_id', $id)->delete();

        // Hapus event
        $this->event->delete($id);

        return redirect()->to('/admin/events')
            ->with('success', 'Event dan semua data terkait berhasil dihapus');
    }
}
