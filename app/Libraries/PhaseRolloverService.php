<?php

namespace App\Libraries;

class PhaseRolloverService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Auto rollover remaining tickets from expired phases to next active phase
     */
    public function rolloverEvent($eventId)
    {
        // Get event date
        $event = $this->db->table('events')->where('id', $eventId)->get()->getRowArray();
        if (!$event) {
            return;
        }

        $eventDate = strtotime($event['event_date']);
        $now = time();
        $diffDays = floor(($eventDate - $now) / 86400);

        // Get all phases ordered by start_day DESC (newest to oldest)
        $phases = $this->db->table('event_prices')
            ->where('event_id', $eventId)
            ->where('use_quota', 1)
            ->orderBy('start_day', 'DESC')
            ->get()
            ->getResultArray();

        if (count($phases) < 2) {
            return; // Need at least 2 phases for rollover
        }

        // Check each phase and rollover if expired
        foreach ($phases as $index => $current) {
            // Check if this phase has expired (past its end_day)
            $hasExpired = $diffDays < (int)$current['end_day'];
            $hasQuota = (int)$current['quota_remaining'] > 0;

            if ($hasExpired && $hasQuota) {
                // Find next phase (next in array since ordered DESC)
                $nextPhase = null;
                for ($i = $index + 1; $i < count($phases); $i++) {
                    if ((int)$phases[$i]['use_quota'] === 1) {
                        $nextPhase = $phases[$i];
                        break;
                    }
                }

                if ($nextPhase) {
                    $amountToTransfer = (int)$current['quota_remaining'];
                    
                    // Set original_quota if not set
                    if ((int)$current['original_quota'] == 0) {
                        $this->db->table('event_prices')
                            ->where('id', $current['id'])
                            ->update(['original_quota' => $current['quota_total']]);
                    }
                    
                    if ((int)$nextPhase['original_quota'] == 0) {
                        $this->db->table('event_prices')
                            ->where('id', $nextPhase['id'])
                            ->update(['original_quota' => $nextPhase['quota_total']]);
                    }

                    // Transfer quota
                    $this->db->transStart();

                    // Update current phase
                    $this->db->table('event_prices')
                        ->where('id', $current['id'])
                        ->update([
                            'quota_remaining' => 0,
                            'transferred_out' => $amountToTransfer
                        ]);

                    // Update next phase
                    $this->db->table('event_prices')
                        ->where('id', $nextPhase['id'])
                        ->update([
                            'quota_total' => $nextPhase['quota_total'] + $amountToTransfer,
                            'quota_remaining' => $nextPhase['quota_remaining'] + $amountToTransfer,
                            'transferred_in' => $nextPhase['transferred_in'] + $amountToTransfer
                        ]);

                    $this->db->transComplete();
                }
            }
        }
    }
}
