LOAD DATA INFILE '~/Downloads/sql.csv' INTO TABLE qantani_payments
	FIELDS TERMINATED BY ';' ENCLOSED BY '"'
	LINES TERMINATED BY '\r\n'
	IGNORE 1 LINES
;
