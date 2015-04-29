ALTER TABLE edd_payments MODIFY COLUMN tax VARCHAR(32) DEFAULT NULL AFTER transaction_id;

ALTER TABLE edd_payments DROP COLUMN `key`;
