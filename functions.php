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
