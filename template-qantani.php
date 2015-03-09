<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

<div class="page-header">
	<h1>
		Uitbetaling Easy iDeal
		<small>Week <?php echo $date_start->format( 'W' ); ?> - <?php echo $date_start->format( 'j M Y' ); ?> t/m <?php echo $date_end->format( 'j M Y' ); ?></small>
	</h1>
</div>

<table class="table table-striped">
	<thead>
		<tr>
			<th scope="col" colspan="6">Easy iDeal</th>
			<th scope="col" colspan="5" class="hidden">Pronamic iDEAL</th>
			<th scope="col" colspan="13">Easy Digital Downloads / WooCommerce</th>
			<th scope="col" colspan="2">Twinfield</th>
		</tr>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Date</th>
			<th scope="col" class="hidden">Currency</th>
			<th scope="col">Amount</th>
			<th scope="col">Description</th>
			<th scope="col">IBAN</th>
			<th scope="col">Ascription</th>
			<th scope="col" class="hidden">Status</th>

			<th scope="col" class="hidden">Title</th>
			<th scope="col" class="hidden">Date</th>
			<th scope="col" class="hidden">Source</th>
			<th scope="col" class="hidden">Source ID</th>
			<th scope="col" class="hidden">Status</th>

			<th scope="col">Bron</th>

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

			<th scope="col">Apart ingeboekt</th>
			<th scope="col">Factuur</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th scope="col" colspan="2"></th>
			<th scope="col"><?php echo format_price( $qantani_total ); ?></th>
			<th scope="col" colspan="13"></th>
			<th scope="col"><?php echo format_price( $source_total ); ?></th>
			<th scope="col"><?php echo format_price( $source_total_tax ); ?></th>
			<th scope="col"></th>
			<th scope="col"><?php echo format_price( $twinfield_total ); ?></th>
			<th scope="col"></th>
		</tr>
	</tfoot>

	<tbody>

		<?php foreach ( $payments as $payment ) : ?>

			<tr>
				<td><?php echo $payment->qantani_id; ?></td>
				<td><?php echo $payment->qantani_date; ?></td>
				<td class="hidden"><?php echo $payment->qantani_currency; ?></td>
				<td><?php echo format_price( $payment->qantani_amount ); ?></td>
				<td><?php echo $payment->qantani_description; ?></td>
				<td><?php echo $payment->qantani_iban; ?></td>
				<td><?php echo $payment->qantani_ascription; ?></td>
				<td class="hidden"><?php echo $payment->qantani_status; ?></td>

				<td class="hidden"><?php echo $payment->pronamic_title; ?></td>
				<td class="hidden"><?php echo $payment->pronamic_date; ?></td>
				<td class="hidden"><?php echo $payment->pronamic_source; ?></td>
				<td class="hidden"><?php echo $payment->pronamic_source_id; ?></td>
				<td class="hidden"><?php echo $payment->pronamic_status; ?></td>

				<?php if ( 'easydigitaldownloads' == $payment->pronamic_source ) : ?>

					<td>Easy Digital Downloads</td>
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

						$link = add_query_arg( array(
							'purchase_id'  => $payment->edd_purchase_id,
							'email'        => $payment->edd_email,
							'purchase_key' => $payment->edd_purchase_key,
						), $payment->edd_site );

						$file = sprintf(
							'%s-edd-invoice-%s.pdf',
							$payment->qantani_id,
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

						?>
					</td>

				<?php elseif ( 'woocommerce' == $payment->pronamic_source ) : ?>

					<td>WooCommerce</td>
					<td><?php echo $payment->wc_billing_company; ?></td>
					<td><?php echo $payment->wc_billing_first_name; ?></td>
					<td><?php echo $payment->wc_billing_last_name; ?></td>
					<td><?php echo $payment->wc_billing_address_1; ?></td>
					<td><?php echo $payment->wc_billing_address_2; ?></td>
					<td><?php echo $payment->wc_billing_postcode; ?></td>
					<td><?php echo $payment->wc_billing_city; ?></td>
					<td></td>
					<td><?php echo $payment->wc_billing_country; ?></td>
					<td><?php echo format_price( $payment->wc_order_total ); ?></td>
					<td><?php echo format_price( $payment->wc_order_tax ); ?></td>
					<td></td>

				<?php else : ?>

					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>

				<?php endif; ?>

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
