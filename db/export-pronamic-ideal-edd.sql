SELECT
	post_payment.ID AS post_payment_id,
	post_payment.post_title AS post_payment_title,
	post_payment.post_date AS post_payment_date,
	post_source.ID AS post_source_id,
	post_source.post_title AS post_source_title,
	post_source.post_date AS post_source_date
FROM
	wp_posts AS post_payment
		INNER JOIN
	wp_postmeta AS meta_source_id
			ON post_payment.ID = meta_source_id.post_id AND meta_source_id.meta_key = '_pronamic_payment_source_id'
		INNER JOIN
	wp_posts AS post_source
			ON post_source.ID = meta_source_id.meta_value
WHERE
	post_payment.post_type = 'pronamic_payment'
;
