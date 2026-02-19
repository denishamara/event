<?php

use App\Models\EventPriceModel;

function getActivePhase(array $event)
{
    $priceModel = new EventPriceModel();

    $eventDate = new DateTime(date('Y-m-d', strtotime($event['event_date'])));
    $today     = new DateTime(date('Y-m-d'));

    $diffDays = (int) $today->diff($eventDate)->format('%r%a');
    if ($diffDays < 0) $diffDays = 0;

    $prices = $priceModel
        ->where('event_id', $event['id'])
        ->orderBy('order_index', 'ASC')
        ->findAll();

    foreach ($prices as $p) {

        // ✅ PRIORITAS 1: CEK QUOTA DAN WAKTU
        if ((int)$p['use_quota'] === 1) {
            // Cek apakah masih dalam rentang waktu phase ini
            $isInTimeRange = $diffDays >= (int)$p['end_day'] && $diffDays <= (int)$p['start_day'];
            
            // Phase aktif jika:
            // 1. Masih ada quota DAN masih dalam rentang waktu
            if ((int)$p['quota_remaining'] > 0 && $isInTimeRange) {
                return $p;
            }
            
            // Jika quota habis ATAU waktu sudah lewat, lanjut ke phase berikutnya
            continue;
        }

        // ✅ PRIORITAS 2: HARI (untuk phase tanpa quota)
        if (
            $diffDays >= (int)$p['end_day'] &&
            $diffDays <= (int)$p['start_day']
        ) {
            return $p;
        }
    }

    return null;
}
