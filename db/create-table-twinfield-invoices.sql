CREATE TABLE `twinfield_invoices` (
	`qantani_payment_id` BIGINT(32) DEFAULT NULL,
	`invoice_number` VARCHAR(64) DEFAULT NULL,
	`separated` BOOLEAN DEFAULT FALSE,
	PRIMARY KEY ( `qantani_payment_id` )
);
