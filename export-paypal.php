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

$year = filter_input( INPUT_GET, 'year', FILTER_SANITIZE_STRING );
$year = empty( $year ) ? date( 'Y' ) : $year;

$date_start = new DateTime();
$date_start->setISODate( $year, $week );

$date_end = new DateTime();
$date_end->setISODate( $year, $week );
$date_end->modify( '+1 week' );

// Statement
$types = array(
	'Betaling winkelwagentje ontvangen',
	'Betaling ontvangen',
);

$query = sprintf( $query, "'" . join("', '", $types ) . "'" );

$statement = $pdo->prepare( $query );

$statement->execute( array(
	$date_start->format( 'Y-m-d' ),
	$date_end->format( 'Y-m-d' ),
	'Voltooid'
) );

$payments = $statement->fetchAll( PDO::FETCH_OBJ );

// Total
$paypal_gross = 0;
$paypal_cost  = 0;
$paypal_net   = 0;
$paypal_tax   = 0;
$edd_amount   = 0;
$edd_tax      = 0;

foreach ( $payments as $payment ) {
	$paypal_gross += $payment->paypal_gross;
	$paypal_cost  += $payment->paypal_cost;
	$paypal_net   += $payment->paypal_net;
	$paypal_tax   += $payment->paypal_tax;
	$edd_amount   += $payment->edd_amount;
	$edd_tax      += $payment->edd_tax;
}

// Export
$slug = sprintf( '%s-week-%s', $year, $week );

$export_dir      = sprintf( 'exports-paypal/%s', $slug );
$export_dir_path = trailingslashit( dirname( __FILE__ ) ) . $export_dir;

wp_mkdir_p( $export_dir_path );

ob_start();

include 'template-paypal.php';

$out = ob_get_contents();

ob_end_clean();

$export_file = sprintf( 'export-%s.html', $slug );
$export_file_path = trailingslashit( $export_dir_path ) . $export_file;

file_put_contents( $export_file_path, $out );

echo $out;