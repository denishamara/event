-- Menambahkan kolom is_read ke tabel chat_messages
-- Jalankan query ini di phpMyAdmin atau MySQL client

ALTER TABLE `chat_messages` 
ADD COLUMN `is_read` TINYINT(1) NOT NULL DEFAULT 0 AFTER `message`;

-- Update semua pesan lama agar ditandai sebagai sudah dibaca
UPDATE `chat_messages` SET `is_read` = 1;
