<?php

// WordPress
define( 'WP_USE_THEMES', false );

require 'wordpress/wp-blog-header.php';

// Load
include 'load.php';

// Query
$query = file_get_contents( 'db/select-paypal-payments.sql' );

/*
echo '<pre>';
echo $query;
echo '</pre>';
*/

// Input
$week = filter_input( INPUT_GET, 'week', FILTER_SANITIZE_STRING );
$week = empty( $week ) ? date( 'W' ) : $week;

$month = filter_input( INPUT_GET, 'month', FILTER_SANITIZE_STRING );
$month = empty( $month ) ? date( 'n' ) : $month;

$year = filter_input( INPUT_GET, 'year', FILTER_SANITIZE_STRING );
$year = empty( $year ) ? date( 'Y' ) : $year;

if ( filter_has_var( INPUT_GET, 'month' ) ) {
	$slug = sprintf( '%s-maand-%s', $year, $month );

	$date_start = new DateTime();
	$date_start->setDate( $year, $month, 1 );

	$date_end = new DateTime();
	$date_end->setDate( $year, $month, 1 );
	$date_end->modify( '+1 month' );
} else {
	$slug = sprintf( '%s-week-%s', $year, $week );

	$date_start = new DateTime();
	$date_start->setISODate( $year, $week );

	$date_end = new DateTime();
	$date_end->setISODate( $year, $week );
	$date_end->modify( '+1 week' );
}

// Statement
$ignore_types = array(
	$pdo->quote( "Omrekening van valuta's" ),
);

$query = sprintf( $query, join(", ", $ignore_types ) );

$statement = $pdo->prepare( $query );

$statement->execute( array(
	$date_start->format( 'Y-m-d' ),
	$date_end->format( 'Y-m-d' ),
	'Voltooid'
) );

$payments = $statement->fetchAll( PDO::FETCH_OBJ );

// Total
$paypal_gross    = 0;
$paypal_cost     = 0;
$paypal_net      = 0;
$paypal_tax      = 0;
$edd_amount      = 0;
$edd_tax         = 0;
$twinfield_total = 0;

$rates = array();

foreach ( $payments as $payment ) {
	$paypal_gross += $payment->paypal_gross;
	$paypal_cost  += $payment->paypal_cost;
	$paypal_net   += $payment->paypal_net;
	$paypal_tax   += $payment->paypal_tax;
	$edd_amount   += $payment->edd_amount;
	$edd_tax      += $payment->edd_tax;

	if ( ! $payment->twinfield_separated ) {
		$twinfield_total += $payment->paypal_net;
	}

	if ( $payment->edd_amount ) {
		$rate = 100 / ( $payment->edd_amount - $payment->edd_tax ) * $payment->edd_tax;
		$rate = round( $rate );

		if ( ! isset( $rates[ $rate ] ) ) {
			$rates[ $rate ] = array(
				'gross' => 0,
				'cost'  => 0,
				'net'   => 0,
				'tax'   => 0,
			);
		}

		$rates[ $rate ]['gross'] += $payment->paypal_gross;
		$rates[ $rate ]['cost']  += $payment->paypal_cost;
		$rates[ $rate ]['net']   += $payment->paypal_net;
		$rates[ $rate ]['tax']   += $payment->paypal_tax;
	}
}

// Export
$export_dir      = sprintf( 'exports-paypal/%s', $slug );
$export_dir_path = trailingslashit( dirname( __FILE__ ) ) . $export_dir;

wp_mkdir_p( $export_dir_path );

ob_start();

include 'template-paypal.php';

$out = ob_get_contents();

ob_end_clean();

$export_file = sprintf( 'paypal-export-%s.html', $slug );
$export_file_path = trailingslashit( $export_dir_path ) . $export_file;

file_put_contents( $export_file_path, $out );

echo $out;
