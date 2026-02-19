<?php

namespace App\Models;

use CodeIgniter\Model;

class EventTicketModel extends Model
{
    protected $table      = 'event_tickets';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'ticket_code',
        'event_id',
        'user_id',
        'payment_id',
        'price',
        'qty',
        'phase',
        'status',
        'ticket_file',
        'qr_data',
        'is_checked_in',
        'checked_in_at'
    ];

    protected $useTimestamps = true;
}
