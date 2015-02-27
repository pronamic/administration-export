<?php

include 'config.php';

// Query
$query = "
	SELECT
		*
	FROM
		edd_payments_meta
	;
";

// Statement
$statement = $pdo->prepare( $query );

$statement->execute( array(

) );

$payments = $statement->fetchAll( PDO::FETCH_OBJ );

// Update
$query = "
	UPDATE
		edd_payments_meta
	SET
		company = ?,
		tax = ?
	WHERE
		id = ?
	;
";

$statement = $pdo->prepare( $query );

// Payments
foreach ( $payments as $payment ) {
	$meta = unserialize( $payment->payment_meta );

	echo $payment->id, '<br />';

	if ( isset( $meta['company'] ) ) {
		$statement->execute( array(
			$meta['company'],
			$meta['tax'],
			$payment->id,
		) );
	}
}
