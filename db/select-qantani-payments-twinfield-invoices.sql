SELECT
	qp.id AS qantani_id,
	qp.date AS qantani_date,
	qp.description AS qantani_description,
	qp.iban AS qantani_iban,
	qp.ascription AS qantani_ascription
FROM
	twinfield_invoices AS ti
		LEFT JOIN
	qantani_payments AS qp
			ON qp.id = ti.qantani_payment_id
WHERE
	invoice_number IS NULL
;
