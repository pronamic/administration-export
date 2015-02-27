<?php

// WordPress
define( 'WP_USE_THEMES', false );

require '../wp-blog-header.php';

// Config
include 'config.php';

// Query
$query = "
	SELECT
		-- Qantani
		qp.id AS qantani_id,
		qp.date_string AS qantani_date,
		qp.currency AS qantani_currency,
		qp.amount AS qantani_amount,
		qp.description AS qantani_description,
		qp.iban AS qantani_iban,
		qp.ascription AS qantani_ascription,
		qp.status AS qantani_status,

		-- Pronamic iDEAL
		pp.title AS pronamic_title,
		pp.date AS pronamic_date,
		pp.source AS pronamic_source,
		pp.source_id AS pronamic_source_id,
		pp.status AS pronamic_status,

		-- Easy Digital Downlaods
		ep.first_name AS edd_first_name,
		ep.last_name AS edd_last_name,
		ep.address AS edd_address,
		ep.address_2 AS edd_address_2,
		ep.city AS edd_city,
		ep.state AS edd_state,
		ep.country AS edd_country,
		ep.zip_code AS edd_zip_code,
		ep.products AS edd_products,
		ep.status AS edd_status,

		epm.company AS edd_company,
		epm.payment_total AS edd_amount,
		epm.tax AS edd_tax,

		ep.email AS edd_email,
		ep.purchase_key AS edd_purchase_key,
		ep.id AS edd_purchase_id,

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
		wc.order_total AS wc_order_total
	FROM
		qantani_payments AS qp
			LEFT JOIN
		pronamic_payments AS pp
				ON qp.id = pp.transaction_id
			LEFT JOIN
		edd_payments AS ep
				ON ep.id = pp.source_id
			LEFT JOIN
		edd_payments_meta AS epm
				ON epm.id = ep.id
			LEFT JOIN
		wc_orders AS wc
				ON wc.id = pp.source_id
	WHERE
		qp.date BETWEEN ? AND ?
			AND
		qp.status = ?
	ORDER BY
		qp.date
	;
";

// Input
$week = filter_input( INPUT_GET, 'week', FILTER_SANITIZE_STRING );
$week = empty( $week ) ? date( 'W' ) : $week;

$year = filter_input( INPUT_GET, 'year', FILTER_SANITIZE_STRING );
$year = empty( $year ) ? date( 'Y' ) : $year;

$date_start = new DateTime();
$date_start->setISODate( $year, $week );

$date_end = new DateTime();
$date_end->setISODate( $year, $week );
$date_end->modify( '+1 week' );

// Statement
$statement = $pdo->prepare( $query );

$statement->execute( array(
	$date_start->format( 'Y-m-d' ),
	$date_end->format( 'Y-m-d' ),
	'paid'
) );

$payments = $statement->fetchAll( PDO::FETCH_OBJ );

// Total
$qantani_total    = 0;
$source_total     = 0;
$source_total_tax = 0;

foreach ( $payments as $payment ) {
	if ( 'paid' == $payment->qantani_status ) {
		$qantani_total += $payment->qantani_amount;
	}

	if ( 'easydigitaldownloads' == $payment->pronamic_source ) {
		$source_total     += $payment->edd_amount;
		$source_total_tax += $payment->edd_tax;
	} elseif ( 'woocommerce' == $payment->pronamic_source ) {
		$source_total     += $payment->wc_order_total;
		$source_total_tax += $payment->wc_order_tax;
	}
}

// Export
$export_dir      = sprintf( '%s-week-%s', $year, $week );
$export_dir_path = trailingslashit( dirname( __FILE__ ) ) . $export_dir;

wp_mkdir_p( $export_dir_path );

ob_start();

include 'template.php';

$out = ob_get_contents();

ob_end_clean();

$export_file = sprintf( 'export-%s.html', $export_dir );
$export_file_path = trailingslashit( $export_dir_path ) . $export_file;

file_put_contents( $export_file_path, $out );

echo $out;
