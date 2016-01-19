<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

<div class="page-header">
	<h1>
		PayPal
		<small>Week <?php echo $date_start->format( 'W' ); ?> - <?php echo $date_start->format( 'j M Y' ); ?> tot <?php echo $date_end->format( 'j M Y' ); ?></small>
	</h1>
</div>

<table class="table table-striped">
	<thead>
		<tr>
			<th scope="col" colspan="7">PayPal</th>
			<th scope="col" colspan="13">Easy Digital Downloads</th>
			<th scope="col" colspan="5">BTW</th>
			<th scope="col" colspan="2">Twinfield</th>
		</tr>
		<tr>
			<th scope="col">Transactie referentie</th>
			<th scope="col">Naam</th>
			<th scope="col">E-mail</th>
			<th scope="col">Type</th>
			<th scope="col">Bruto</th>
			<th scope="col">Kosten</th>
			<th scope="col">Netto</th>
			<th scope="col">BTW</th>

			<th scope="col">Bedrijf</th>
			<th scope="col">Voornaam</th>
			<th scope="col">Achternaam</th>
			<th scope="col">Adres</th>
			<th scope="col">Adres 2</th>
			<th scope="col">Postcode</th>
			<th scope="col">Stad</th>
			<th scope="col">Provincie</th>
			<th scope="col">Land</th>
			<th scope="col">Bedrag</th>
			<th scope="col">BTW</th>

			<th scope="col">Factuur</th>

			<th scope="col">BTW-nummer</th>
			<th scope="col">Valide</th>
			<th scope="col">Bedrijfsnaam</th>
			<th scope="col">Adres</th>
			<th scope="col">BTW verlegd</th>

			<th scope="col">Apart ingeboekt</th>
			<th scope="col">Factuur</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th scope="col" colspan="3"></th>
			<th scope="col"></th>
			<th scope="col"></th>
			<th scope="col"><?php echo format_price( $paypal_cost ); ?></th>
			<th scope="col"></th>
			<th scope="col"></th>

			<th scope="col" colspan="9"></th>

			<th scope="col"><?php echo format_price( $edd_amount ); ?></th>
			<th scope="col"><?php echo format_price( $edd_tax ); ?></th>

			<th scope="col"></th>

			<th scope="col" colspan="5"></th>

			<th scope="col"><?php echo format_price( $twinfield_total ); ?></th>
			<th scope="col"></th>
		</tr>
	</tfoot>

	<tbody>

		<?php foreach ( $payments as $payment ) : ?>

			<tr>
				<td><?php echo $payment->paypal_transaction_reference; ?></td>
				<td><?php echo $payment->paypal_name; ?></td>
				<td><?php echo $payment->paypal_email_from; ?></td>
				<td><?php echo $payment->paypal_type; ?></td>
				<td>
					<?php

					echo format_price( $payment->paypal_gross, $payment->paypal_curency );

					if ( isset( $payment->converted_currency ) ) {
						echo ' ';
						echo '<em>';
						echo '(';
						echo format_price( $payment->converted_gross, $payment->converted_currency );							
						echo ')';
						echo '</em>';
					}

					?>
				</td>
				<td><?php echo format_price( $payment->paypal_cost, $payment->paypal_curency ); ?></td>
				<td><?php echo format_price( $payment->paypal_net, $payment->paypal_curency ); ?></td>
				<td><?php echo format_price( $payment->paypal_tax, $payment->paypal_curency ); ?></td>

				<td><?php echo $payment->edd_company; ?></td>
				<td><?php echo $payment->edd_first_name; ?></td>
				<td><?php echo $payment->edd_last_name; ?></td>
				<td><?php echo $payment->edd_address; ?></td>
				<td><?php echo $payment->edd_address_2; ?></td>
				<td><?php echo $payment->edd_zip_code; ?></td>
				<td><?php echo $payment->edd_city; ?></td>
				<td><?php echo $payment->edd_state; ?></td>
				<td><?php echo $payment->edd_country; ?></td>
				<td><?php echo format_price( $payment->edd_amount ); ?></td>
				<td><?php echo format_price( $payment->edd_tax ); ?></td>

				<td>
					<?php

					if ( ! empty( $payment->edd_purchase_id ) ) {
						$link = add_query_arg( array(
							'purchase_id'  => $payment->edd_purchase_id,
							'email'        => $payment->edd_email,
							'purchase_key' => $payment->edd_purchase_key,
						), $payment->edd_site );

						$file = sprintf(
							'%s-edd-invoice-%s.pdf',
							$payment->paypal_transaction_reference,
							$payment->edd_purchase_id
						);

						$export_file = trailingslashit( $export_dir_path ) . $file;

						if ( ! is_readable( $export_file ) ) {
							$response = wp_remote_get( $link, array(
								'stream'   => true,
								'filename' => $export_file,
							) );
						}

						if ( is_readable( $export_file ) ) {
							printf(
								'<a href="%s">%s</a>',
								esc_attr( trailingslashit( $export_dir ) . $file ),
								esc_html( $payment->edd_purchase_id )
							);
						}
					}

					?>
				</td>

				<td>
					<?php echo $payment->ed_vat_number; ?>
				</td>
				<td>
					<?php

					if ( $payment->ed_vat_number_valid ) {
						echo 'Ja';
					} else {
						echo 'Nee';
					}

					?>
				</td>
				<td>
					<?php echo $payment->ed_vat_company_name; ?>
				</td>
				<td>
					<?php echo nl2br( $payment->ed_vat_company_address ); ?>
				</td>
				<td>
					<?php

					if ( $payment->ed_vat_reversed_charged ) {
						echo 'Ja';
					} else {
						echo 'Nee';
					}

					?>
				</td>

				<td>
					<?php

					if ( $payment->twinfield_separated ) {
						echo 'Ja';
					} else {
						echo 'Nee';
					}

					?>
				</td>
				<td>
					<?php

					if ( ! empty( $payment->twinfield_invoice_number ) ) {
						printf(
							'<a href="%s">%s</a>',
							esc_attr( sprintf( 'http://in.pronamic.nl/facturen/%s/', $payment->twinfield_invoice_number ) ),
							esc_html( $payment->twinfield_invoice_number )
						);
					}

					?>
				</td>
			</tr>

		<?php endforeach; ?>

	</tbody>
</table>

<h2>Overzicht</h2>

<table class="table table-striped" style="width: auto;">
	<tbody>
		<tr>
			<th scope="row">Bruto</th>
			<td><?php echo format_price( $paypal_gross ); ?></td>
		</tr>
		<tr>
			<th scope="row">Kosten</th>
			<td><?php echo format_price( $paypal_cost ); ?></td>
		</tr>
		<tr>
			<th scope="row">Netto</th>
			<td><?php echo format_price( $paypal_net ); ?></td>
		</tr>
		<tr>
			<th scope="row">BTW</th>
			<td><?php echo format_price( $paypal_tax ); ?></td>
		</tr>
	</tbody>
</table>

<?php foreach ( $rates as $rate => $data ) : ?>

	<h2>Tarief <?php echo $rate; ?>%</h2>

	<table class="table table-striped" style="width: auto;">
		<tbody>
			<tr>
				<th scope="row">Bruto</th>
				<td><?php echo format_price( $data['gross'] ); ?></td>
			</tr>
			<tr>
				<th scope="row">Kosten</th>
				<td><?php echo format_price( $data['cost'] ); ?></td>
			</tr>
			<tr>
				<th scope="row">Netto</th>
				<td><?php echo format_price( $data['net'] ); ?></td>
			</tr>
			<tr>
				<th scope="row">BTW</th>
				<td><?php echo format_price( $data['tax'] ); ?></td>
			</tr>
		</tbody>
	</table>

<?php endforeach; ?>
