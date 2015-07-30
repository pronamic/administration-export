<?php

// WordPress
define( 'WP_USE_THEMES', false );

require 'wordpress/wp-blog-header.php';

// Load
include 'load.php';

// Query
$query = file_get_contents( 'db/select-qantani-payments.sql' );

/*
echo '<pre>';
echo $query;
echo '</pre>';
*/

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
$twinfield_total  = 0;

$rates = array();

foreach ( $payments as $payment ) {
	if ( 'paid' == $payment->qantani_status ) {
		$qantani_total += $payment->qantani_amount;

		if ( ! $payment->twinfield_separated ) {
			$twinfield_total += $payment->qantani_amount;
		}
	}

	if ( 'easydigitaldownloads' == $payment->pronamic_source ) {
		$source_total     += $payment->edd_amount;
		$source_total_tax += $payment->edd_tax;
	} elseif ( 'woocommerce' == $payment->pronamic_source ) {
		$source_total     += $payment->wc_order_total;
		$source_total_tax += $payment->wc_order_tax;
	}

	if ( $payment->edd_amount ) {
		$rate = 100 / ( $payment->edd_amount - $payment->edd_tax ) * $payment->edd_tax;
		$rate = round( $rate );

		if ( ! isset( $rates[ $rate ] ) ) {
			$rates[ $rate ] = array(
				'gross' => 0,
				'tax'   => 0,
			);
		}

		$rates[ $rate ]['gross'] += $payment->edd_amount;
		$rates[ $rate ]['tax']   += $payment->edd_tax;
	}
}

// Export
$slug = sprintf( '%s-week-%s', $year, $week );

$export_dir      = sprintf( 'exports-qantani/%s', $slug );
$export_dir_path = trailingslashit( dirname( __FILE__ ) ) . $export_dir;

wp_mkdir_p( $export_dir_path );

ob_start();

include 'template-qantani.php';

$out = ob_get_contents();

ob_end_clean();

$export_file = sprintf( 'qantani-export-%s.html', $slug );
$export_file_path = trailingslashit( $export_dir_path ) . $export_file;

file_put_contents( $export_file_path, $out );

echo $out;
