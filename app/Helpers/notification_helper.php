<?php

if (!function_exists('get_pending_payments_count')) {
    /**
     * Get count of pending payments
     */
    function get_pending_payments_count()
    {
        $paymentModel = new \App\Models\PaymentModel();
        return $paymentModel->where('status', 'pending')->countAllResults();
    }
}

if (!function_exists('get_pending_refunds_count')) {
    /**
     * Get count of pending refund requests
     */
    function get_pending_refunds_count()
    {
        $refundModel = new \App\Models\RefundModel();
        return $refundModel->where('status', 'pending')->countAllResults();
    }
}

if (!function_exists('get_unread_chats_count')) {
    /**
     * Get count of chats with unread messages for admin/agent
     */
    function get_unread_chats_count()
    {
        $db = \Config\Database::connect();
        
        // Get chats that have unread messages from users (not from admin/agent)
        $builder = $db->table('chats c');
        $builder->select('COUNT(DISTINCT c.id) as count');
        $builder->join('chat_messages cm', 'cm.chat_id = c.id');
        $builder->join('users u', 'u.id = cm.sender_id');
        $builder->where('cm.is_read', 0);
        $builder->where('u.role', 'user'); // Only count messages from users
        
        $result = $builder->get()->getRow();
        return $result ? (int)$result->count : 0;
    }
}
