<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $allowedFields = [
        'event_ticket_id',
        'method',
        'amount',
        'qty',
        'status',
        'proof_file'
    ];
}
