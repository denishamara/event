<?php

namespace App\Controllers;

use App\Models\ChatModel;
use App\Models\ChatMessageModel;

class AdminChatController extends BaseController
{
    protected $chat;
    protected $message;

    public function __construct()
    {
        $this->chat    = new ChatModel();
        $this->message = new ChatMessageModel();
    }

    public function index()
    {
    if (!session()->get('isLogin')) {
        return redirect()->to('/login');
    }

    if (session()->get('role') === 'user') {
        return redirect()->to('/events');
    }

    $chats = $this->chat
        ->select('chats.*, users.name AS user_name')
        ->join('users', 'users.id = chats.user_id')
        ->orderBy('chats.id', 'DESC')
        ->findAll();

    // Hitung pesan yang belum dibaca untuk setiap chat
    foreach ($chats as &$chat) {
        // Hitung pesan dari user (bukan dari admin) yang belum dibaca
        $unreadCount = $this->message
            ->where('chat_id', $chat['id'])
            ->where('sender_id', $chat['user_id']) // Pesan dari user
            ->where('is_read', 0) // Belum dibaca
            ->countAllResults();
        
        $chat['unread_count'] = $unreadCount;
    }

    return view('admin/chat_list', [
        'chats' => $chats
    ]);
    }

    public function show($chatId)
    {
        if (!session()->get('isLogin')) {
            return redirect()->to('/login');
        }

        // Ambil info chat untuk mendapatkan user_id
        $chat = $this->chat->find($chatId);
        
        if ($chat) {
            // Tandai semua pesan dari user di chat ini sebagai sudah dibaca
            $this->message
                ->where('chat_id', $chatId)
                ->where('sender_id', $chat['user_id']) // Hanya pesan dari user
                ->set(['is_read' => 1])
                ->update();
        }

        $messages = $this->message
            ->where('chat_id', $chatId)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return view('admin/chat_show', [
            'chat_id'  => $chatId,
            'messages' => $messages
        ]);
    }

    public function send()
    {
        if (!session()->get('isLogin')) {
            return redirect()->to('/login');
        }

        $this->message->insert([
            'chat_id'   => $this->request->getPost('chat_id'),
            'sender_id' => session()->get('user_id'),
            'message'   => $this->request->getPost('message'),
            'is_read'   => 1 // Pesan dari admin otomatis ditandai sudah dibaca
        ]);

        return redirect()->back();
    }
}
