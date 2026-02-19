<?php

namespace App\Models;

use CodeIgniter\Model;

class RefundModel extends Model
{
    protected $table = 'refunds';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'ticket_id',
        'user_id',
        'reason',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = false;
}
