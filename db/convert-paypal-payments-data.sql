UPDATE
	`paypal_payments`
SET
	`date` = STR_TO_DATE( CONCAT( `date_string`, ' ', `time_string` ), '%d-%m-%Y %H:%i:%s' ),
	`gross` = REPLACE( REPLACE( `gross_string`, '.', '' ), ',', '.' ),
	`cost` = REPLACE( REPLACE( `cost_string`, '.', '' ), ',', '.' ),
	`net` = REPLACE( REPLACE( `net_string`, '.', '' ), ',', '.' ),
	`tax` = REPLACE( REPLACE( `tax_string`, '.', '' ), ',', '.' )
;
