SELECT
	post.ID AS id,
	post.post_title AS title,
	post.post_date AS order_date,

	MAX( IF( meta.meta_key = "_billing_company", meta.meta_value, NULL ) ) AS billing_company,
	MAX( IF( meta.meta_key = "_billing_first_name", meta.meta_value, NULL ) ) AS billing_first_name,
	MAX( IF( meta.meta_key = "_billing_last_name", meta.meta_value, NULL ) ) AS billing_last_name,
	MAX( IF( meta.meta_key = "_billing_address_1", meta.meta_value, NULL ) ) AS billing_address_1,
	MAX( IF( meta.meta_key = "_billing_address_2", meta.meta_value, NULL ) ) AS billing_address_2,
	MAX( IF( meta.meta_key = "_billing_postcode", meta.meta_value, NULL ) ) AS billing_postcode,
	MAX( IF( meta.meta_key = "_billing_city", meta.meta_value, NULL ) ) AS billing_city,
	MAX( IF( meta.meta_key = "_billing_country", meta.meta_value, NULL ) ) AS billing_country,
	MAX( IF( meta.meta_key = "_billing_email", meta.meta_value, NULL ) ) AS billing_email,
	MAX( IF( meta.meta_key = "_billing_phone", meta.meta_value, NULL ) ) AS billing_phone,

	MAX( IF( meta.meta_key = "_order_tax", meta.meta_value, NULL ) ) AS order_tax,
	MAX( IF( meta.meta_key = "_order_total", meta.meta_value, NULL ) ) AS order_total
FROM
	wp_posts AS post
		LEFT JOIN
	wp_postmeta AS meta
			ON post.ID = meta.post_id
WHERE
	post.post_type = 'shop_order'
GROUP BY
	post.ID
;
