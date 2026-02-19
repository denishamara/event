<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\EventPriceModel;
use DateTime;

class CronPriceController extends BaseController
{
    public function rollover()
    {
        $eventModel = new EventModel();
        $priceModel = new EventPriceModel();

        $events = $eventModel->findAll();
        $transferCount = 0;

        foreach ($events as $event) {

            // Hitung H- berapa hari dari event
            $eventDate = new DateTime(date('Y-m-d', strtotime($event['event_date'])));
            $today     = new DateTime(date('Y-m-d'));
            $diffDays = (int) $today->diff($eventDate)->format('%r%a');
            if ($diffDays < 0) $diffDays = 0;

            $prices = $priceModel
                ->where('event_id', $event['id'])
                ->orderBy('order_index', 'ASC')
                ->findAll();

            for ($i = 0; $i < count($prices) - 1; $i++) {

                $current = $prices[$i];
                $next    = $prices[$i + 1];

                // Cek apakah phase saat ini sudah melewati batas waktu (end_day)
                // Atau masih punya quota remaining
                $hasExpired = $diffDays < (int)$current['end_day'];
                $hasQuota = (int)$current['quota_remaining'] > 0;

                // Jika phase sudah expired dan masih ada quota, transfer ke phase berikutnya
                if ($current['use_quota'] && $hasQuota && $hasExpired) {
                    
                    // Transfer quota ke phase berikutnya
                    $quotaToTransfer = (int)$current['quota_remaining'];
                    
                    // Update quota phase berikutnya
                    $priceModel->update($next['id'], [
                        'quota_total' => (int)$next['quota_total'] + $quotaToTransfer,
                        'quota_remaining' => (int)$next['quota_remaining'] + $quotaToTransfer
                    ]);

                    // Kosongkan quota phase saat ini
                    $priceModel->update($current['id'], [
                        'quota_remaining' => 0
                    ]);

                    $transferCount++;
                    
                    log_message('info', sprintf(
                        'Phase rollover: Event ID %d, Phase "%s" expired (H-%d), transferred %d quota to "%s" at price %s',
                        $event['id'],
                        $current['phase'],
                        $diffDays,
                        $quotaToTransfer,
                        $next['phase'],
                        number_format($next['price'], 0, ',', '.')
                    ));
                }
            }
        }

        echo "ROLL OVER DONE - {$transferCount} phase(s) transferred based on time expiry";
    }
}
