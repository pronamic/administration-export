ALTER TABLE `paypal_payments`
	ADD COLUMN `balance` NUMERIC(15,2) DEFAULT NULL AFTER `tax`
;
