<?php

// Functions
function format_price( $amount ) {
	if ( '' == $amount ) {
		return '';
	}

	return '&euro;&nbsp;' . number_format( $amount, 2, ',', '.' );
}
