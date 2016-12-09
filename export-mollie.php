<?php

// Load
include 'load.php';

// Query
$query = file_get_contents( 'db/select-mollie-payments.sql' );

/*
echo '<pre>';
echo $query;
echo '</pre>';
*/

// Input
$month = filter_input( INPUT_GET, 'month', FILTER_SANITIZE_STRING );
$month = empty( $month ) ? date( 'n' ) : $month;

$year = filter_input( INPUT_GET, 'year', FILTER_SANITIZE_STRING );
$year = empty( $year ) ? date( 'Y' ) : $year;

$date_start = new DateTime();
$date_start->setDate( $year, $month, 1 );

$date_end = new DateTime();
$date_end->setDate( $year, $month, 0 );
$date_end->modify( '+1 month' );

// Query
$conditions = '';

// Statuses
$statuses = array(
	//$pdo->quote( 'paid' ),
	$pdo->quote( 'paidout' ),
	$pdo->quote( 'refunded' ),
);

$conditions .= sprintf(
	' AND mp.status IN (%s)',
	join( ", ", $statuses )
);

$query = sprintf( $query, $conditions );

// Statement
$statement = $pdo->prepare( $query );

$parameters = array(
	$date_start->format( 'Y-m-d' ),
	$date_end->format( 'Y-m-d' )
);

$statement->execute( $parameters );

$payments = $statement->fetchAll( PDO::FETCH_OBJ );

// Total
$mollie_total    = 0;
$source_total     = 0;
$source_total_tax = 0;
$twinfield_total  = 0;

$rates = array();

foreach ( $payments as $payment ) {
	if ( 'paidout' == $payment->mollie_status ) {
		$mollie_total += $payment->mollie_amount;

		if ( ! $payment->twinfield_separated ) {
			$twinfield_total += $payment->mollie_amount;
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
$slug = sprintf( '%s-maand-%s', $year, $month );

$export_dir      = sprintf( 'exports-mollie/%s', $slug );
$export_dir_path = trailingslashit( dirname( __FILE__ ) ) . $export_dir;

wp_mkdir_p( $export_dir_path );

ob_start();

include 'template-mollie.php';

$out = ob_get_contents();

ob_end_clean();

$export_file = sprintf( 'mollie-export-%s.html', $slug );
$export_file_path = trailingslashit( $export_dir_path ) . $export_file;

file_put_contents( $export_file_path, $out );

echo $out;
