SELECT
	post_payment.ID AS id,
	post_payment.post_title AS title,
	post_payment.post_date AS payment_date,
	meta_total.meta_value AS payment_total,
	meta_payment.meta_value AS payment_meta
FROM
	wp_posts AS post_payment
		INNER JOIN
	wp_postmeta AS meta_total
			ON post_payment.ID = meta_total.post_id AND meta_total.meta_key = '_edd_payment_total'
		INNER JOIN
	wp_postmeta AS meta_payment
			ON post_payment.ID = meta_payment.post_id AND meta_payment.meta_key = '_edd_payment_meta'
WHERE
	post_payment.post_type = 'edd_payment'
;
