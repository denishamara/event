<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\EventPriceModel;

class EventController extends BaseController
{
    protected $event;
    protected $price;
    protected $db;

    public function __construct()
    {
        $this->event = new EventModel();
        $this->price = new EventPriceModel();
        $this->db    = \Config\Database::connect();
    }

    /* ======================================================
       BUILD EVENT DATA (DIPAKAI 2 HALAMAN)
    ====================================================== */
    private function buildEvents(array $events)
{
    $userId = session()->get('user_id');

    // ambil favorite
    $favoriteIds = [];

    if ($userId) {
        $rows = $this->db->table('event_favorites')
            ->select('event_id')
            ->where('user_id', $userId)
            ->get()
            ->getResultArray();

        foreach ($rows as $r) {
            $favoriteIds[] = $r['event_id'];
        }
    }

    foreach ($events as &$event) {

        $event['is_expired'] = strtotime($event['event_date']) < time();

        $prices = $this->price
            ->where('event_id', $event['id'])
            ->orderBy('order_index','ASC')
            ->findAll();

        /* ===============================
           REGULAR EVENT
        =============================== */
        if (count($prices) === 0) {

            $remaining = (int)$event['quota'];

            $event['remaining_tickets'] = $remaining;

            if ($event['is_expired']) {
                $event['price_phase'] = 'Event Ended';
                $event['display_price'] = $event['price'];
            }
            elseif ($remaining <= 0) {
                $event['price_phase'] = 'Sold Out';
                $event['display_price'] = $event['price'];
            }
            else {
                $event['price_phase'] = 'Regular';
                $event['display_price'] = $event['price'];
            }

        }

        /* ===============================
           PHASE EVENT
        =============================== */
        else {

            // Hitung hari sampai event (sama seperti AdminEventController & CheckoutController)
            $eventDate = new \DateTime(date('Y-m-d', strtotime($event['event_date'])));
            $today = new \DateTime(date('Y-m-d'));
            $daysUntilEvent = (int) $today->diff($eventDate)->format('%r%a');
            
            if ($daysUntilEvent < 0) {
                $daysUntilEvent = 0;
            }

            $remaining = 0;
            foreach ($prices as $p) {
                $remaining += (int)$p['quota_remaining'];
            }

            $event['remaining_tickets'] = $remaining;

            // Cari phase yang aktif berdasarkan HARI (bukan quota)
            $activePhaseByDate = null;
            foreach ($prices as $p) {
                // Phase aktif jika: end_day <= daysUntilEvent <= start_day
                $isInTimeRange = $daysUntilEvent >= (int)$p['end_day'] && $daysUntilEvent <= (int)$p['start_day'];
                
                if ($isInTimeRange) {
                    $activePhaseByDate = $p;
                    break;
                }
            }

            if ($event['is_expired']) {
                $event['price_phase'] = 'Event Ended';
                $event['display_price'] = $event['price'];
            }
            elseif ($remaining <= 0) {
                $event['price_phase'] = 'Sold Out';
                $event['display_price'] = $event['price'];
            }
            elseif ($activePhaseByDate) {
                // Gunakan phase berdasarkan hari, bukan quota
                $event['price_phase'] = $activePhaseByDate['phase'];
                $event['display_price'] = $activePhaseByDate['price'];
            }
            else {
                $event['price_phase'] = 'Available';
                $event['display_price'] = $event['price'];
            }
        }

        $event['is_almost_soldout'] =
            !$event['is_expired'] &&
            $event['remaining_tickets'] > 0 &&
            $event['remaining_tickets'] <= 10;

        $event['is_favorite'] = in_array($event['id'], $favoriteIds);
    }

    return $events;
}

    /* ===============================
       ALL EVENTS
    =============================== */
    public function index()
    {
        // Filter hanya event yang belum lewat dan urut berdasarkan event_date DESC (terbaru di atas)
        $events = $this->event
            ->where('event_date >=', date('Y-m-d H:i:s'))
            ->orderBy('event_date', 'DESC')
            ->findAll();
        $events = $this->buildEvents($events);

        return view('events/index', [
            'events' => $events
        ]);
    }

    public function show($id)
{
    $event = $this->event->find($id);
    if (!$event) return redirect()->to('/events');

    $event['is_expired'] = strtotime($event['event_date']) < time();

    $prices = $this->price
        ->where('event_id', $event['id'])
        ->orderBy('order_index','ASC')
        ->findAll();

    /* ======================================================
       FIX: REGULAR EVENT (TANPA PHASE)
    ====================================================== */
    if (count($prices) === 0) {

        $event['remaining_tickets'] = (int)$event['quota'];

        if ($event['is_expired']) {
            $event['display_price'] = $event['price'];
            $event['price_phase']   = 'Event Ended';
        }
        elseif ($event['remaining_tickets'] <= 0) {
            $event['display_price'] = $event['price'];
            $event['price_phase']   = 'Sold Out';
        }
        else {
            $event['display_price'] = $event['price'];
            $event['price_phase']   = 'Regular';
        }

        return view('events/show', [
            'event' => $event,
            'priceRanges' => [],
            'activePriceId' => null,
            'diffDays' => 0,
            'isExpired' => $event['is_expired']
        ]);
    }

    /* ======================================================
       EVENT DENGAN PHASE
    ====================================================== */

    // Hitung hari sampai event (sama seperti AdminEventController & CheckoutController)
    $eventDate = new \DateTime(date('Y-m-d', strtotime($event['event_date'])));
    $today = new \DateTime(date('Y-m-d'));
    $daysUntilEvent = (int) $today->diff($eventDate)->format('%r%a');
    
    if ($daysUntilEvent < 0) {
        $daysUntilEvent = 0;
    }

    $remaining = 0;
    foreach ($prices as $p) {
        $remaining += (int)$p['quota_remaining'];
    }

    $event['remaining_tickets'] = $remaining;

    // EVENT SUDAH LEWAT
    if ($event['is_expired']) {
        return view('events/show', [
            'event' => $event,
            'priceRanges' => $prices,
            'activePriceId' => null,
            'diffDays' => 0,
            'isExpired' => true
        ]);
    }

    // ACTIVE PHASE - Berdasarkan HARI bukan quota
    $activePhaseByDate = null;
    foreach ($prices as $p) {
        // Phase aktif jika: end_day <= daysUntilEvent <= start_day
        $isInTimeRange = $daysUntilEvent >= (int)$p['end_day'] && $daysUntilEvent <= (int)$p['start_day'];
        
        if ($isInTimeRange) {
            $activePhaseByDate = $p;
            break;
        }
    }

    if ($remaining <= 0) {
        $event['display_price'] = $event['price'];
        $event['price_phase']   = 'Sold Out';
        $activePriceId = null;

    } elseif ($activePhaseByDate) {
        $event['display_price'] = $activePhaseByDate['price'];
        $event['price_phase']   = $activePhaseByDate['phase'];
        $activePriceId = $activePhaseByDate['id'];

    } else {
        $event['display_price'] = $event['price'];
        $event['price_phase']   = 'Available';
        $activePriceId = null;
    }

    return view('events/show', [
        'event' => $event,
        'priceRanges' => $prices,
        'activePriceId' => $activePriceId,
        'diffDays' => $daysUntilEvent,
        'isExpired' => false
    ]);
}

    /* ===============================
       FAVORITE PAGE
    =============================== */
    public function favorites()
    {
        if (!session()->get('isLogin')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        $events = $this->db->table('events')
            ->select('events.*')
            ->join('event_favorites','event_favorites.event_id = events.id')
            ->where('event_favorites.user_id', $userId)
            ->orderBy('event_favorites.created_at','DESC')
            ->get()
            ->getResultArray();

        $events = $this->buildEvents($events);

        return view('events/favorites', [
            'events' => $events
        ]);
    }

    /* ===============================
       TOGGLE FAVORITE
    =============================== */
    public function toggleFavorite($eventId)
    {
        if (!session()->get('isLogin')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        $fav = $this->db->table('event_favorites')
            ->where('user_id',$userId)
            ->where('event_id',$eventId)
            ->get()
            ->getRow();

        if ($fav) {
            $this->db->table('event_favorites')->delete(['id'=>$fav->id]);
        } else {
            $this->db->table('event_favorites')->insert([
                'user_id' => $userId,
                'event_id' => $eventId
            ]);
        }

        return redirect()->back();
    }
}
