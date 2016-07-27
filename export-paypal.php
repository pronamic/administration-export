<?php

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

	$date_start = new DateTime( 'today midnight' );
	$date_start->setISODate( $year, $week );

	$date_end = new DateTime( 'today midnight' );
	$date_end->setISODate( $year, $week );
	$date_end->modify( '+1 week' );
}

// Statement
$ignore_types = array(
	//$pdo->quote( "Omrekening van valuta's" ),
);

// Conditions
$data = array(
	$date_start->format( 'Y-m-d' ),
	$date_end->format( 'Y-m-d' )
);

$conditions = '';

// Statuses
$statuses = array(
	$pdo->quote( 'Voltooid' ), 
	$pdo->quote( 'Verrekend' ),
);

$conditions .= sprintf(
	' AND pp.status IN (%s)',
	join(", ", $statuses )
);

if ( filter_has_var( INPUT_GET, 'currency' ) ) {
	$currency = filter_input( INPUT_GET, 'currency', FILTER_SANITIZE_STRING );

	$conditions .= ' AND pp.currency = ?';
	$data[] = $currency;
}

$query = sprintf( $query, $conditions );

$statement = $pdo->prepare( $query );

$statement->execute( $data );

$payments = $statement->fetchAll( PDO::FETCH_OBJ );

// Currency Conversion
$query = file_get_contents( 'db/select-paypal-currency-conversion-payment.sql' );

$currency_conversion_statement = $pdo->prepare( $query );

// Total
$paypal_gross    = 0;
$paypal_cost     = 0;
$paypal_net      = 0;
$paypal_tax      = 0;
$edd_amount      = 0;
$edd_tax         = 0;
$twinfield_total = 0;

$rates = array();

$twinfield = array();

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

	if ( 'EUR' !== $payment->paypal_curency ) {
		$exchange_rate = administratie_get_paypal_exchange_rate( $currency_conversion_statement, $payment );

		if ( false !== $exchange_rate ) {
			$payment->converted_currency = $payment->paypal_curency;
			$payment->converted_gross    = $payment->paypal_gross;
			$payment->converted_cost     = $payment->paypal_cost;
			$payment->converted_net      = $payment->paypal_net;
			$payment->converted_tax      = $payment->paypal_tax;
			$payment->converted_balance  = $payment->paypal_balance;

			$payment->paypal_curency = 'EUR';
			$payment->paypal_gross *= $exchange_rate;
			$payment->paypal_cost *= $exchange_rate;
			$payment->paypal_net *= $exchange_rate;
			$payment->paypal_tax *= $exchange_rate;
			$payment->paypal_balance *= $exchange_rate;
		}
	}

	if ( ! isset( $twinfield[ 'costs' ] ) ) {
		$twinfield[ 'costs' ] = (object) array(
			'description'                    => 'PayPal kosten',
			'name'                           => '',
			'amount_inclusive_tax_and_costs' => 0,
			'tax_rate'                       => 0,
			'tax'                            => 0,
			'amount_exclusive_tax'           => 0,
			'tax_extra'                      => '',
			'currency'                       => 'EUR',
		);
	}

	if ( $payment->edd_amount ) {
		$rate = 100 / ( $payment->edd_amount - $payment->edd_tax ) * $payment->edd_tax;
		$rate = round( $rate );

		if ( $payment->ed_vat_reversed_charged ) {
			// @see https://github.com/woothemes/woocommerce/blob/2.5.3/includes/class-wc-tax.php#L54-L58
			$twinfield[ $payment->paypal_transaction_reference ] = (object) array(
				'description'                    => $payment->paypal_transaction_reference,
				'name'                           => $payment->paypal_name,
				'amount_inclusive_tax_and_costs' => $payment->paypal_gross,
				'tax_rate'                       => $rate,
				'tax'                            => $payment->paypal_tax,
				'amount_exclusive_tax'           => $payment->paypal_gross - $payment->paypal_tax,
				'tax_extra'                      => '' . $payment->edd_country . ', ' . $payment->ed_vat_number,
				'currency'                       => $payment->paypal_curency,
			);
		} else {
			if ( ! isset( $twinfield[ $rate ] ) ) {
				$twinfield[ $rate ] = (object) array(
					'description'                    => 'Inkomsten ' . $rate . '%',
					'name'                           => '',
					'amount_inclusive_tax_and_costs' => 0,
					'tax_rate'                       => $rate,
					'tax'                            => 0,
					'amount_exclusive_tax'           => 0,
					'tax_extra'                      => '',
					'currency'                       => 'EUR',
				);
			}

			$twinfield[ $rate ]->amount_inclusive_tax_and_costs += $payment->paypal_gross;
			$twinfield[ $rate ]->tax                            += $payment->paypal_tax;
			$twinfield[ $rate ]->amount_exclusive_tax           += ( $payment->paypal_gross - $payment->paypal_tax );
		}

		$twinfield[ 'costs' ]->amount_inclusive_tax_and_costs += $payment->paypal_cost;
		$twinfield[ 'costs' ]->tax                            += 0;
		$twinfield[ 'costs' ]->amount_exclusive_tax           += $payment->paypal_cost;

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
	} else {
		$twinfield[ $payment->paypal_transaction_reference ] = (object) array(
			'description'                    => $payment->paypal_transaction_reference,
			'name'                           => $payment->paypal_name,
			'amount_inclusive_tax_and_costs' => $payment->paypal_gross,
			'tax_rate'                       => '',
			'tax'                            => $payment->paypal_tax,
			'amount_exclusive_tax'           => $payment->paypal_gross - $payment->paypal_tax,
			'tax_extra'                      => '',
			'currency'                       => $payment->paypal_curency,
		);

		$twinfield[ 'costs' ]->amount_inclusive_tax_and_costs += $payment->paypal_cost;
		$twinfield[ 'costs' ]->tax                            += 0;
		$twinfield[ 'costs' ]->amount_exclusive_tax           += $payment->paypal_cost;
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
