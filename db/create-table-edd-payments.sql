CREATE TABLE `edd_payments` (
	`site` VARCHAR(128) DEFAULT NULL,

	`id` BIGINT(32) DEFAULT NULL,
	`title` VARCHAR(64) DEFAULT NULL,
	`date` VARCHAR(64) DEFAULT NULL,
	`status` VARCHAR(64) DEFAULT NULL,

	`total` VARCHAR(64) DEFAULT NULL,
	`customer_id` VARCHAR(64) DEFAULT NULL,
	`user_email` VARCHAR(64) DEFAULT NULL,
	`user_ip` VARCHAR(64) DEFAULT NULL,
	`purchase_key` VARCHAR(64) DEFAULT NULL,
	`gateway` VARCHAR(64) DEFAULT NULL,
	`transaction_id` VARCHAR(64) DEFAULT NULL,

	## Tax
	`tax` VARCHAR(32) DEFAULT NULL,
	`vat_number` VARCHAR(32) DEFAULT NULL,
	`vat_number_valid` VARCHAR(1) DEFAULT NULL,
	`vat_company_name` VARCHAR(32) DEFAULT NULL,
	`vat_company_address` VARCHAR(256) DEFAULT NULL,
	`vat_reversed_charged` VARCHAR(1) DEFAULT NULL,

	## Meta
	`meta` TEXT DEFAULT NULL,

	## Converted from meta
	`converted` BOOLEAN DEFAULT FALSE,
	`currency` VARCHAR(8) DEFAULT NULL,

	`user_id` VARCHAR(8) DEFAULT NULL,
	`first_name` VARCHAR(32) DEFAULT NULL,
	`last_name` VARCHAR(32) DEFAULT NULL,
	`discount` VARCHAR(32) DEFAULT NULL,
	`address` VARCHAR(32) DEFAULT NULL,
	`address_2` VARCHAR(32) DEFAULT NULL,
	`city` VARCHAR(32) DEFAULT NULL,
	`state` VARCHAR(32) DEFAULT NULL,
	`country` VARCHAR(32) DEFAULT NULL,
	`zip` VARCHAR(32) DEFAULT NULL,

	`company` VARCHAR(32) DEFAULT NULL,

	`products` VARCHAR(256) DEFAULT NULL,

	PRIMARY KEY ( `site`, `id` )
);
