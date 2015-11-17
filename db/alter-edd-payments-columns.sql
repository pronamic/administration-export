ALTER TABLE `edd_payments` MODIFY COLUMN `tax` VARCHAR(32) DEFAULT NULL AFTER `transaction_id`;

ALTER TABLE `edd_payments` DROP COLUMN `key`;

ALTER TABLE `edd_payments`
	ADD COLUMN `vat_number` VARCHAR(32) DEFAULT NULL AFTER `tax`,
	ADD COLUMN `vat_number_valid` VARCHAR(1) DEFAULT NULL AFTER `vat_number`,
	ADD COLUMN `vat_company_name` VARCHAR(32) DEFAULT NULL AFTER `vat_number_valid`,
	ADD COLUMN `vat_company_address` VARCHAR(256) DEFAULT NULL AFTER `vat_company_name`,
	ADD COLUMN `vat_reversed_charged` VARCHAR(1) DEFAULT NULL AFTER `vat_company_address`
;
