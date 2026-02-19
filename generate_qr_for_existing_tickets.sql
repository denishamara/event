-- Generate QR data for existing paid tickets
UPDATE event_tickets 
SET qr_data = CONCAT('TKT-', id, '-', UUID(), '-', SHA2(CONCAT(ticket_code, UNIX_TIMESTAMP()), 256))
WHERE status = 'paid' 
AND (qr_data IS NULL OR qr_data = '');

-- Check hasil
SELECT id, ticket_code, qr_data, is_checked_in 
FROM event_tickets 
WHERE status = 'paid' 
LIMIT 10;
