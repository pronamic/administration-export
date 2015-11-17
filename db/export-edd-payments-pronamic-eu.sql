SELECT
	'http://www.pronamic.eu/' AS site,

	post.ID AS id,
	post.post_title AS title,
	post.post_date AS payment_date,
	post.post_status AS payment_status,

	MAX( IF( meta.meta_key = "_edd_payment_total", meta.meta_value, NULL ) ) AS payment_total,
	MAX( IF( meta.meta_key = "_edd_payment_customer_id", meta.meta_value, NULL ) ) AS payment_customer_id,
	MAX( IF( meta.meta_key = "_edd_payment_user_email", meta.meta_value, NULL ) ) AS payment_user_email,
	MAX( IF( meta.meta_key = "_edd_payment_user_ip", meta.meta_value, NULL ) ) AS payment_user_ip,
	MAX( IF( meta.meta_key = "_edd_payment_purchase_key", meta.meta_value, NULL ) ) AS payment_purchase_key,
	MAX( IF( meta.meta_key = "_edd_payment_gateway", meta.meta_value, NULL ) ) AS payment_gateway,
	MAX( IF( meta.meta_key = "_edd_payment_transaction_id", meta.meta_value, NULL ) ) AS payment_transaction_id,
	MAX( IF( meta.meta_key = "_edd_payment_tax", meta.meta_value, NULL ) ) AS payment_tax,
	MAX( IF( meta.meta_key = "_edd_payment_vat_number", meta.meta_value, NULL ) ) AS payment_vat_number,
	MAX( IF( meta.meta_key = "_edd_payment_vat_number_valid", meta.meta_value, NULL ) ) AS payment_vat_number_valid,
	MAX( IF( meta.meta_key = "_edd_payment_vat_company_name", meta.meta_value, NULL ) ) AS payment_vat_company_name,
	MAX( IF( meta.meta_key = "_edd_payment_vat_company_address", meta.meta_value, NULL ) ) AS payment_vat_company_address,
	MAX( IF( meta.meta_key = "_edd_payment_vat_reversed_charged", meta.meta_value, NULL ) ) AS payment_vat_reversed_charged,
	MAX( IF( meta.meta_key = "_edd_payment_meta", meta.meta_value, NULL ) ) AS payment_meta
FROM
	wp_2_posts AS post
		LEFT JOIN
	wp_2_postmeta AS meta
			ON post.ID = meta.post_id
WHERE
	post.post_type = 'edd_payment'
GROUP BY
	post.ID
;
