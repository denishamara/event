<?php

namespace App\Controllers;

use App\Models\ChatModel;
use App\Models\ChatMessageModel;

class ChatController extends BaseController
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

        $chat = $this->chat
            ->where('user_id', session()->get('user_id'))
            ->first();

        if (!$chat) {
            $chatId = $this->chat->insert([
                'user_id' => session()->get('user_id')
            ]);
        } else {
            $chatId = $chat['id'];
        }

        $messages = $this->message
            ->where('chat_id', $chatId)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return view('chat/index', [
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
            'is_read'   => 0 // Pesan dari user ditandai belum dibaca oleh admin
        ]);

        return redirect()->back();
    }
}
