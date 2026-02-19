<?php

namespace App\Models;

use CodeIgniter\Model;

class EventPriceModel extends Model
{
    protected $table = 'event_prices';
    protected $allowedFields = [
        'event_id', 'phase', 'order_index', 'price', 'start_day', 'end_day', 'use_quota', 'quota_total', 'quota_remaining'
    ];
}
