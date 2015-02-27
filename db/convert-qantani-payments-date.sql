UPDATE
	`qantani_payments`
SET
	`date` = STR_TO_DATE( `date_string`, '%d-%m-%Y %H:%i:%s' )
;
