<?php
/**
 * E-Mails
 *
 * @since       1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the License Key details to the users Order Email
 */
function wc_slm_email_content($order, $is_admin_email) {
	if ( $order->post->post_status == 'wc-completed' ) {
		$output = '';

		// Check if licenses were generated
		$licenses = get_post_meta( $order->post->ID, '_wc_slm_payment_licenses', true );

		if ( $licenses && count( $licenses ) != 0 ) {
			$output .= '<h3>' . __( 'Your Licenses', 'wc-slm' ) . ':</h3>';
			$output .= '<table>';
			$output .= '<tr><th class="td">' . __( 'Item', 'wc-slm' ) . '</th>';
			$output .= '<th class="td">' . __( 'License', 'wc-slm' ) . '</th>';
			$output .= '<th class="td">' . __( 'Expire Date', 'wc-slm' ) . '</th></tr>';

			foreach ( $licenses as $license ) {
				$output .= '<tr>';
				if ( isset( $license['item'] ) && isset( $license['key'] ) ) {
					$output .= '<td class="td">' . $license['item'] . '</td>';
					$output .= '<td class="td">' . $license['key'] . '</td>';
				} else {
					$output .= '<td class="td"> </td>';
					$output .= '<td class="td"> </td>';
				}
				if ( isset( $license['expires'] ) ) {
					$output .= '<td class="td">' . $license['expires'] . '</td>';
				}
				else {
					$output .= '<td class="td"> </td>';
				}
				$output .= '</tr>';
			}
			$output .= '</table>';
		}

		echo $output;
	}
}
add_action( 'woocommerce_email_before_order_table', 'wc_slm_email_content', 10, 2 );
