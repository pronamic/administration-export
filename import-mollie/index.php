<?php

include '../load.php';

$api_key = filter_input( INPUT_GET, 'api_key', FILTER_SANITIZE_STRING );

$parameters = filter_input_array( INPUT_GET, array(
	'count'  => FILTER_VALIDATE_INT,
	'offset' => FILTER_VALIDATE_INT,
) );

$parameters = is_array( $parameters ) ? $parameters : array();

$url = 'https://api.mollie.nl/v1/payments';
$url = add_query_arg( $parameters, $url );

$response = wp_remote_get( $url, array(
	'headers'   => array(
		'Authorization' => 'Bearer ' . $api_key,
	),
) );

$statement = $pdo->prepare( '
	INSERT
	INTO mollie_payments(
		id,
		resource,
		mode,
		`date`,
		status,
		amount,
		description,
		method
	)
	VALUES (
		:id,
		:resource,
		:mode,
		:date,
		:status,
		:amount,
		:description,
		:method
	);
' );

if ( '200' == wp_remote_retrieve_response_code( $response ) ) {
	$body = wp_remote_retrieve_body( $response );

	$result = json_decode( $body );

	var_dump( $result->links );

	foreach ( $result->data as $payment ) {
		$date = new DateTime( $payment->createdDatetime );

		echo $payment->id, '<br />';

		$statement->bindValue( ':id', $payment->id );
		$statement->bindValue( ':resource', $payment->resource );
		$statement->bindValue( ':mode', $payment->mode );
		$statement->bindValue( ':date', $date->format( 'Y-m-d H:i:s' ) );
		$statement->bindValue( ':status', $payment->status );
		$statement->bindValue( ':amount', $payment->amount );
		$statement->bindValue( ':description', $payment->description );
		$statement->bindValue( ':method', $payment->method );

		$result = $statement->execute();

		var_dump( $result );
	}
}
