SELECT
	post.ID AS id,
	post.post_title AS title,
	post.post_date AS payment_date,

	MAX( IF( meta.meta_key = "_pronamic_payment_source", meta.meta_value, NULL ) ) AS source,
	MAX( IF( meta.meta_key = "_pronamic_payment_source_id", meta.meta_value, NULL ) ) AS source_id,
	MAX( IF( meta.meta_key = "_pronamic_payment_currency", meta.meta_value, NULL ) ) AS currency,
	MAX( IF( meta.meta_key = "_pronamic_payment_amount", meta.meta_value, NULL ) ) AS amount,
	MAX( IF( meta.meta_key = "_pronamic_payment_email", meta.meta_value, NULL ) ) AS email,
	MAX( IF( meta.meta_key = "_pronamic_payment_transaction_id", meta.meta_value, NULL ) ) AS transaction_id,
	MAX( IF( meta.meta_key = "_pronamic_payment_status", meta.meta_value, NULL ) ) AS status
FROM
	wp_posts AS post
		LEFT JOIN
	wp_postmeta AS meta
			ON post.ID = meta.post_id
WHERE
	post.post_type = 'pronamic_payment'
GROUP BY
	post.ID
;
