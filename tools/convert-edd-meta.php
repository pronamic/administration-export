<?php

include '../load.php';

// Query
$query = "
	SELECT
		*
	FROM
		edd_payments
	WHERE
		NOT converted
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
		edd_payments
	SET
		currency = :currency,
		user_id = :user_id,
		first_name = :first_name,
		last_name = :last_name,
		discount = :discount,
		address = :address,
		address_2 = :address_2,
		city = :city,
		state = :state,
		country = :country,
		zip = :zip,
		products = :products,
		company = :company,
		converted = :converted
	WHERE
		id = :id
	;
";

$statement = $pdo->prepare( $query );

// Payments
foreach ( $payments as $payment ) {
	$meta = unserialize( $payment->meta );

	$products = array();
	if ( isset( $meta['cart_details'] ) ) {
		foreach ( $meta['cart_details'] as $item ) {
			$products[] = sprintf(
				'%s - %s',
				$item['name'],
				$item['price']
			);
		}
	}

	$data = array(
		'currency'        => @$meta['currency'],
		'user_id'         => @$meta['user_info']['id'],
		'first_name'      => @$meta['user_info']['first_name'],
		'last_name'       => @$meta['user_info']['last_name'],
		'discount'        => @$meta['user_info']['discount'],
		'address'         => @$meta['user_info']['address']['line1'],
		'address_2'       => @$meta['user_info']['address']['line2'],
		'city'            => @$meta['user_info']['address']['city'],
		'state'           => @$meta['user_info']['address']['state'],
		'country'         => @$meta['user_info']['address']['country'],
		'zip'             => @$meta['user_info']['address']['zip'],
		'company'         => @$meta['company'],
		'products'        => implode( ', ', $products ),
		'converted'       => true,
		'id'              => $payment->id,
	);

	echo $payment->id, '<br />';

	$result = $statement->execute( $data );

	var_dump( $result );
}
