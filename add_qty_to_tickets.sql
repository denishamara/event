-- Add qty column to event_tickets table
ALTER TABLE `event_tickets` 
ADD COLUMN `qty` INT(11) DEFAULT 1 AFTER `price`;

-- Update existing tickets to have qty = 1
UPDATE `event_tickets` SET `qty` = 1 WHERE `qty` IS NULL;
