SELECT
	-- Mollie
	mp.id AS mollie_id,
	mp.date AS mollie_date,
	mp.amount AS mollie_amount,
	mp.description AS mollie_description,
	mp.status AS mollie_status,

	-- Pronamic iDEAL
	pp.title AS pronamic_title,
	pp.date AS pronamic_date,
	pp.source AS pronamic_source,
	pp.source_id AS pronamic_source_id,
	pp.status AS pronamic_status,

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
	ep.id AS edd_purchase_id,

	ep.vat_number AS ed_vat_number,
	ep.vat_number_valid AS ed_vat_number_valid,
	ep.vat_company_name AS ed_vat_company_name,
	ep.vat_company_address AS ed_vat_company_address,
	ep.vat_reversed_charged AS ed_vat_reversed_charged,

	-- WooCommerce
	wc.billing_company AS wc_billing_company,
	wc.billing_first_name AS wc_billing_first_name,
	wc.billing_last_name AS wc_billing_last_name,
	wc.billing_address_1 AS wc_billing_address_1,
	wc.billing_address_2 AS wc_billing_address_2,
	wc.billing_postcode AS wc_billing_postcode,
	wc.billing_city AS wc_billing_city,
	wc.billing_country AS wc_billing_country,
	wc.billing_email AS wc_billing_email,
	wc.billing_phone AS wc_billing_phone,
	wc.order_tax AS wc_order_tax,
	wc.order_total AS wc_order_total,

	-- Twinfield
	t.invoice_number AS twinfield_invoice_number,
	t.separated AS twinfield_separated
FROM
	mollie_payments AS mp
		LEFT JOIN
	pronamic_payments AS pp
			ON mp.id = pp.transaction_id
		LEFT JOIN
	edd_payments AS ep
			ON ( ep.id = pp.source_id AND ep.site = pp.site )
		LEFT JOIN
	wc_orders AS wc
			ON wc.id = pp.source_id
		LEFT JOIN
	twinfield_invoices AS t
			ON t.mollie_payment_id = mp.id
WHERE
	mp.date BETWEEN ? AND ?
	%s
ORDER BY
	mp.date
;
