-- Add password reset fields to users table
ALTER TABLE `users` 
ADD COLUMN `reset_token` VARCHAR(100) NULL DEFAULT NULL,
ADD COLUMN `reset_token_expires` DATETIME NULL DEFAULT NULL;
