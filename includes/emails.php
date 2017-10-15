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
 * Add the License Key details to the users Order email
 */
function wc_slm_email_content($order, $is_admin_email) {
	if ( get_post_status( $order->get_id() ) === 'wc-completed' ) {
		$output = '';

		wc_slm_log_msg( __( 'Order Completed. Adding License Key details for Order ID ', 'wc-slm' ) . $order->get_id() . ' to Order email' );

		// Check if licenses were generated
		$licenses = get_post_meta( $order->get_id(), '_wc_slm_payment_licenses', true );

		if ( $licenses && count( $licenses ) != 0 ) {
			wc_slm_log_msg( __( 'License Key(s) found. Generating output for email content', 'wc-slm' ) );
			$output .= '<h3>' . __( 'Your Licenses', 'wc-slm' ) . ':</h3>';
			$output .= '<table class="td" style="width: 100%; margin-bottom: 40px; color: #737373; border: 1px solid #e4e4e4;" cellspacing="0" cellpadding="6" border="1">';
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
			wc_slm_log_msg( __( 'Adding License Key details to Order email', 'wc-slm' ) );
		}

		echo $output;
	}
}
add_action( 'woocommerce_email_after_order_table', 'wc_slm_email_content', 10, 2 );
