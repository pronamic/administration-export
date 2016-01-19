SELECT
	-- PayPal
	pp.transaction_reference AS paypal_transaction_reference,
	pp.name AS paypal_name,
	pp.email_from AS paypal_email_from,
	pp.type AS paypal_type,
	pp.date AS paypal_date,
	pp.currency AS paypal_curency,
	pp.gross AS paypal_gross,
	pp.cost AS paypal_cost,
	pp.net AS paypal_net,
	pp.tax AS paypal_tax
FROM
	paypal_payments AS pp
WHERE
	pp.date = :date
		AND
	pp.currency = :currency
		AND
	(
		pp.ref_id_transaction = :transaction_reference
			OR
		pp.ref_id_transaction = :ref_id_transaction
	)
		AND
	pp.type = :type
;
