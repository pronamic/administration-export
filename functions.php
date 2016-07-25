<?php

// Functions
function format_price( $amount, $currency = 'EUR' ) {
	if ( '' == $amount ) {
		return '';
	}

	$symbol = '';
	switch ( $currency ) {
		case 'EUR' : 
			$symbol = '&euro;';

			break;
		case 'USD' :
		case 'AUD' :
			$symbol = '$';

			break;
		default : 
			$symbol = $currency;

			break;
	}

	return $symbol . '&nbsp;' . number_format( $amount, 2, ',', '.' );
}

function administratie_maybe_display_converted_currency( $amount, $currency ) {
	if ( empty( $amount ) ) {
		return;
	}

	echo ' ';
	echo '<em>';
	echo '(';
	echo format_price( $amount, $currency );							
	echo ')';
	echo '</em>';
}

function administratie_get_paypal_exchange_rate( $statement, $payment ) {
	$statement->execute( array(
		':date'                  => $payment->paypal_date,
		':currency'              => 'EUR',
		//':transaction_reference' => $payment->paypal_transaction_reference,
		//':ref_id_transaction'    => $payment->paypal_ref_id_transaction,
		':type'                  => "Omrekening van valuta's",
	) );

	$payment_euro = $statement->fetch( PDO::FETCH_OBJ );

	$statement->execute( array(
		':date'                  => $payment->paypal_date,
		':currency'              => $payment->paypal_curency,
		//':transaction_reference' => $payment->paypal_transaction_reference,
		//':ref_id_transaction'    => $payment->paypal_ref_id_transaction,
		':type'                  => "Omrekening van valuta's",
	) );

	$payment_other = $statement->fetch( PDO::FETCH_OBJ );

	if ( $payment_euro && $payment_other ) {
		$euro_amount  = abs( (float) $payment_euro->paypal_gross );
		$other_amount = abs( (float) $payment_other->paypal_gross );

		$exchange_rate = $euro_amount / $other_amount;

		return $exchange_rate;
	}

	// Fixer.io
	$date = new DateTime( $payment->paypal_date );

	$url = sprintf( 'http://api.fixer.io/%s?base=%s', $date->format( 'Y-m-d' ), $payment->paypal_curency );

	$response = file_get_contents( $url );

	if ( $response !== false ) {
		$data = json_decode( $response );

		if ( $data !== false ) {
			$exchange_rate = $data->rates->EUR;

			return $exchange_rate;
		}
	}

	return false;
}
