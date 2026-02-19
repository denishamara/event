-- Add QR Code fields to event_tickets table
ALTER TABLE event_tickets 
ADD COLUMN qr_data VARCHAR(255) NULL AFTER ticket_code,
ADD COLUMN is_checked_in TINYINT(1) DEFAULT 0 AFTER qr_data,
ADD COLUMN checked_in_at DATETIME NULL AFTER is_checked_in,
ADD INDEX idx_qr_data (qr_data),
ADD INDEX idx_is_checked_in (is_checked_in);

-- Check hasil
SELECT 
    id, 
    ticket_code, 
    qr_data, 
    is_checked_in, 
    checked_in_at 
FROM event_tickets 
LIMIT 5;
