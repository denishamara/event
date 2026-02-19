<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatModel extends Model
{
    protected $table      = 'chats';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id'
    ];

    protected $useTimestamps = false;
}
