<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table      = 'events';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'title',
        'description',
        'location',
        'event_date',
        'price',
        'quota',
        'image'
    ];

    protected $useTimestamps = true;
}
