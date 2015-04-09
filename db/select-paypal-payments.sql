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
	pp.tax AS paypal_tax,

	-- Easy Digital Downlaods
	ep.site AS edd_site,
	ep.first_name AS edd_first_name,
	ep.last_name AS edd_last_name,
	ep.address AS edd_address,
	ep.address_2 AS edd_address_2,
	ep.city AS edd_city,
	ep.state AS edd_state,
	ep.country AS edd_country,
	ep.zip AS edd_zip_code,
	'' AS edd_products,
	ep.status AS edd_status,

	ep.company AS edd_company,
	ep.total AS edd_amount,
	ep.tax AS edd_tax,

	ep.user_email AS edd_email,
	ep.purchase_key AS edd_purchase_key,
	ep.id AS edd_purchase_id
FROM
	paypal_payments AS pp
		LEFT JOIN
	edd_payments AS ep
			ON ep.transaction_id = pp.transaction_reference
WHERE
	pp.date BETWEEN ? AND ?
		AND
	pp.status = ?
		AND
	pp.type NOT IN (%s)
ORDER BY
	pp.date
;
