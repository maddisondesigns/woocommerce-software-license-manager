<?php
/**
 * Purchase
 *
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * On purchase complete
 *
 * @since 1.0.0
 * @return void
 */
function wc_slm_on_complete_purchase( $order_id ) {
	wc_slm_log_msg( __( 'Start of Software License Key creation', 'wc-slm' ) );
	if ( WC_SLM_API_URL != '' && WC_SLM_API_SECRET != '' ) {
		wc_slm_log_msg( __( 'API URL and API Secret Supplied. Attempting to create License key', 'wc-slm' ) );
		wc_slm_create_license_keys( $order_id );
	}
}
add_action( 'woocommerce_order_status_completed', 'wc_slm_on_complete_purchase', 10, 1 );

/**
 * Create license key
 *
 * @since 1.0.0
 * @return void
 */
function wc_slm_create_license_keys( $order_id ) {

	// Get Order details
	$_order = wc_get_order( $order_id );

/*	// Get User ID from order
	$user_id = $_order->get_user_id();

	// Get User details
	$user_info = get_userdata( $user_id );

	// Get User Meta
	$get_user_meta = get_user_meta( $user_id );
	$payment_meta['user_info']['first_name'] = $get_user_meta['billing_first_name'][0];
	$payment_meta['user_info']['last_name'] = $get_user_meta['billing_last_name'][0];
	$payment_meta['user_info']['email'] = $get_user_meta['billing_email'][0];
	$payment_meta['user_info']['company'] = $get_user_meta['billing_company'][0];*/

    // Get User Meta    
    $payment_meta['user_info']['first_name'] = $_order->get_billing_first_name();
    $payment_meta['user_info']['last_name'] = $_order->get_billing_last_name();
    $payment_meta['user_info']['email'] = $_order->get_billing_email();
    $payment_meta['user_info']['company'] = $_order->get_billing_company();

	// Collect license keys
	$licenses = array();

	// Get an array of items/products within this order
	$items = $_order->get_items();

	foreach ( $items as $item => $values ) {
		$download_id = $product_id = $values['product_id'];
		$product = new WC_Product( $product_id );

		wc_slm_log_msg( __( 'Checking if licensing is enabled', 'wc-slm' ) );
		if ( wc_slm_is_licensing_enabled( $product_id ) ) {

			wc_slm_log_msg( __( 'Checking if product is Downloadable', 'wc-slm' ) );
			if ( $product->is_downloadable() ) {

				wc_slm_log_msg( __( 'Product is Downloadable', 'wc-slm' ) );
				$download_quantity = absint( $values['qty'] );

				for ( $i = 1; $i <= $download_quantity; $i++ ) {

					// Calculate Expire date
					$renewal_period = (int) wc_slm_get_licensing_renewal_period( $product_id );
					if ( $renewal_period == 0 ) {
						wc_slm_log_msg( __( 'License Renewal Period for Product ID ', 'wc-slm' ) . $product_id . __( ' is set to Lifetime', 'wc-slm' ) );
						$renewal_period = '0000-00-00';
					} else {
						wc_slm_log_msg( __( 'License Renewal Period for Product ID ', 'wc-slm' ) . $product_id . __( ' is set to ', 'wc-slm' ) . $renewal_period . _n( ' year', ' years', $renewal_period, 'wc-slm' ) );
						$renewal_period = date( 'Y-m-d', strtotime( '+' . $renewal_period . ' years' ) );
					}

					// Sites allowed
					$sites_allowed = wc_slm_get_sites_allowed( $product_id );
					wc_slm_log_msg( __( 'Product ID ', 'wc-slm' ) . $product_id . __( ' can be assigned to ', 'wc-slm' ) . $sites_allowed . __( ' sites', 'wc-slm' ) );
					if ( !$sites_allowed ) {
						$sites_allowed_error = __( 'License could not be created: Invalid sites allowed number.', 'wc-slm' );
						$int = wc_insert_payment_note( $order_id, $sites_allowed_error );
						wc_slm_log_msg( $sites_allowed_error );
						break;
					}

					// Transaction id
					$transaction_id = wc_get_payment_transaction_id( $product_id );

					// Build item name
					$item_name = $product->get_title();
					// $item_name = $product->get_formatted_name();

					// Build parameters
					wc_slm_log_msg( __( 'Building query to send to the Software License Manager', 'wc-slm' ) );
					$api_params = array();
					$api_params['slm_action'] = 'slm_create_new';
					$api_params['secret_key'] = WC_SLM_API_SECRET;
					$api_params['first_name'] = ( isset( $payment_meta['user_info']['first_name'] ) ) ? $payment_meta['user_info']['first_name'] : '';
					$api_params['last_name'] = ( isset( $payment_meta['user_info']['last_name'] ) ) ? $payment_meta['user_info']['last_name'] : '';
					$api_params['email'] = ( isset( $payment_meta['user_info']['email'] ) ) ? $payment_meta['user_info']['email'] : '';
					$api_params['company_name'] = $payment_meta['user_info']['company'];
					$api_params['product_ref'] = $product->get_name();
					/**
					 * Set TXN ID to $order_id instead of $product_id
					 * @since 1.0.7
					 * @ref https://wordpress.org/support/topic/qty-1-generates-same-license
					 */
					$api_params['txn_id'] = $order_id;
					$api_params['max_allowed_domains'] = $sites_allowed;
					$api_params['date_created'] = date( 'Y-m-d' );
					$api_params['date_expiry'] = $renewal_period;

					// Send query to the license manager server
					$url = WC_SLM_API_URL . '?' . http_build_query( $api_params );

					wc_slm_log_msg( __( 'Attempting to create License Key for ', 'wc-slm' ) . $api_params['first_name'] . ' ' . $api_params['last_name'] . ' for Product ' . $api_params['product_ref'] );
					$response = wp_remote_get( $url, array(
						'timeout' => 20,
						'sslverify' => false
					) );

					// Get license key
					$license_key = wc_slm_get_license_key( $response );

					// Collect license keys
					if ( $license_key ) {
						wc_slm_log_msg( __( 'SUCCESS! License Key created for ', 'wc-slm' ) . $api_params['first_name'] . ' ' . $api_params['last_name'] );
						$licenses[] = array(
							'item' => $item_name,
							'key' => $license_key,
							'expires' => $renewal_period
						);
					}
				}
			}

		} else {
			wc_slm_log_msg( __( 'Licensing is not enabled for Product ID ', 'wc-slm' ) . $product_id );
		}
	}

	// Payment note
	wc_slm_payment_note( $order_id, $licenses );

	// Assign licenses
	wc_slm_assign_licenses( $order_id, $licenses );
}

/**
 * Get generated license key
 *
 * @since 1.0.0
 * @return mixed
 */
function wc_slm_get_license_key( $response ) {
	// Check for error in the response
	if ( is_wp_error( $response ) ) {
		wc_slm_log_msg( __( 'Error! Unable to Create License Key.', 'wc-slm' ) );
		return false;
	}

	// Get License data
	$json = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', utf8_encode( wp_remote_retrieve_body( $response ) ) );
	$license_data = json_decode( $json );

	if ( !isset( $license_data->key ) ) {
		wc_slm_log_msg( __( 'Error! License created but can\'t retrieve Key', 'wc-slm' ) );
		return false;
	}

	// Prepare note text
	return $license_data->key;
}

/**
 * Leave payment not for license creation
 *
 * @since 1.0.0
 * @return void
 */
function wc_slm_payment_note( $order_id, $licenses ) {

	if ( $licenses && count( $licenses ) != 0 ) {
		$message = __( 'License Key(s) generated', 'wc-slm' );

		foreach ( $licenses as $license ) {
			$message .= '<br />' . $license['item'] . ': ' . $license['key'];
		}
		wc_slm_log_msg( __( 'Payment Note created for Order ', 'wc-slm' ) . $order_id );

	} else {
		wc_slm_log_msg( __( 'Error! License Key(s) could not be created or was not enabled on Product ID ', 'wc-slm' ) . $order_id );
		$message = __( 'License Key(s) could not be created or was not enabled on product.', 'wc-slm' );
	}

	// Save note
	$int = wc_insert_payment_note( $order_id, $message );
	wc_slm_log_msg( __( 'Payment Note saved for Order ', 'wc-slm' ) . $order_id );
}

/**
 * Assign generated license keys to payments
 *
 * @since 1.0.0
 * @return void
 */
function wc_slm_assign_licenses( $order_id, $licenses ) {

	if ( count( $licenses ) != 0 ) {
		wc_slm_log_msg( __( 'License Key assigned to Order ', 'wc-slm' ) . $order_id );
		update_post_meta( $order_id, '_wc_slm_payment_licenses', $licenses );
	} else {
		wc_slm_log_msg( __( 'License Key does not exist so cannot assign to order', 'wc-slm' ) );
	}
}

/**
 * Get sites allowed from download.
 *
 * @since  1.0.0
 * @return mixed
 */
function wc_slm_get_sites_allowed( $product_id ) {

	$wc_slm_sites_allowed = absint( get_post_meta( $product_id, '_wc_slm_sites_allowed', true ) );

	if ( empty( $wc_slm_sites_allowed ) ) {
		return false;
	}

	return $wc_slm_sites_allowed;
}
/**
 * Get sites allowed from download.
 *
 * @since  1.0.0
 * @return mixed
 */
function wc_slm_get_licensing_renewal_period( $product_id ) {

	$wc_slm_sites_allowed = absint( get_post_meta( $product_id, '_wc_slm_licensing_renewal_period', true ) );

	if ( empty( $wc_slm_sites_allowed ) ) {
		return 0;
	}

	return $wc_slm_sites_allowed;
}

/**
 * Check if licensing for a certain download is enabled
 *
 * @since  1.0.0
 * @return bool
 */
function wc_slm_is_licensing_enabled( $download_id ) {
	$licensing_enabled = absint( get_post_meta( $download_id, '_wc_slm_licensing_enabled', true ) );

	// Set defaults
	if ( $licensing_enabled ) {
		wc_slm_log_msg( __( 'Licensing for Product ID ', 'wc-slm' ) . $download_id . __( ' is ENABLED', 'wc-slm' ) );
		return true;
	} else {
		wc_slm_log_msg( __( 'Licensing for Product ID ', 'wc-slm' ) . $download_id . __( ' is DISABLED', 'wc-slm' ) );
		return false;
	}
}

/**
 * Insert the Payment Note message into the Order
 *
 * @since  1.0.0
 * @return bool
 */
function wc_insert_payment_note( $order_id, $msg ) {
	$order = new WC_Order( $order_id );
	$order->add_order_note( $msg );
}

/**
 * Get the Transaction ID
 *
 * @since  1.0.0
 * @return bool
 */
function wc_get_payment_transaction_id( $order_id ) {
	return get_post_meta( $order_id, '_transaction_id', true );
}

/**
 * Add License details to user account View Order page
 *
 * @since 1.0.3
 */
function wc_slm_lic_order_meta( $order ) {
	$output = '';
	$licenses = get_post_meta( $order->get_id(), '_wc_slm_payment_licenses', true );

	if ( $licenses && count( $licenses ) != 0 ) {

		wc_slm_log_msg( __( 'Customer ID ', 'wc-slm' ) . $order->get_customer_id() . __( ' is viewing License Keys for Order ID ', 'wc-slm' ) . $order->get_id() );
		$output .= '<h3>' . __( 'Your Licenses', 'wc-slm' ) . ':</h3>';
		$output .= '<table class="shop_table shop_table_responsive">';
		$output .= '<tr><th class="td">' . __( 'Item', 'wc-slm' ) . '</th>';
		$output .= '<th class="td">' . __( 'License', 'wc-slm' ) . '</th></tr>';

		foreach ( $licenses as $license ) {
			$output .= '<tr>';
			if ( isset( $license['item'] ) && isset( $license['key'] ) ) {
				$output .= '<td class="td"><strong>' . $license['item'] . '</strong>';
				// If the verification secret key has been set, get the list of registered domains for this License Key
				if ( defined( 'WC_SLM_API_SECRET_VERFIY' ) ) {
					$registered_domains = wc_slm_check_license( $license['key'] );
					if ( $registered_domains != false ) {
						$registered_domains = explode( ',', $registered_domains );
						$output .= '</br>Registered Domains:';
						foreach ( $registered_domains as $domain ) {
							$output .= '</br>&nbsp;&nbsp;' . $domain;
						}
					}
				}
				$output .= '</td>';
				$output .= '<td class="td" style="vertical-align:top;">' . $license['key'] . '</td>';
			} else {
				$output .= '<td class="td">' . __( 'No Item assigned', 'wc-slm' ) . '</td>';
				$output .= '<td class="td">' . __( 'No License Key assigned', 'wc-slm' ) . '</td>';
			}
			$output .= '</tr>';
		}
		$output .= '</table>';
	}

	if ( isset( $output ) ) {
		echo $output;
	}
}
add_action( 'woocommerce_order_details_after_order_table', 'wc_slm_lic_order_meta', 10, 1 );

/**
 * Check the license is valid and get the list of currently registered/activated domains
 *
 * @since 2.0.0
 */
function wc_slm_check_license( $license ) {
	$return_val = false;

	$api_params = array(
		'secret_key' => WC_SLM_API_SECRET_VERFIY,
		'slm_action' => 'slm_check',
		'license_key' => $license
		);

	wc_slm_log_msg( __( 'Verifying License Key and Retrieving Registered Domains', 'wc-slm' ) );
	// Call the Software License Manager API.
	$response = wp_remote_get(
		add_query_arg( $api_params, trailingslashit( WC_SLM_API_URL ) ),
		array(
			'timeout' => 15,
			'sslverify' => false
		)
	);

	// Make sure the response returned ok before continuing any further
	if ( is_wp_error( $response ) ) {
		wc_slm_log_msg( __( 'Error! Invalid response when verifying License Key', 'wc-slm' ) );
		return false;
	}

	if ( is_array( $response ) ) {
		$json = $response['body'];
		$json = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', utf8_encode($json) );
		$license_data = json_decode( $json );
	}

	if ( $license_data->result == 'success' ) {
		wc_slm_log_msg( __( 'License Key verified. Retrieving registered domains', 'wc-slm' ) );
		$registered_domains = array();
		foreach ( $license_data->registered_domains as $domain ) {
			$registered_domains[] = $domain->registered_domain;
		}
		if ( !empty( $registered_domains ) ) {
			$return_val = implode( ',', $registered_domains );
		}
		else {
			$return_val = false;
		}
	}
	else {
		wc_slm_log_msg( __( 'Error! Unable to verify License Key', 'wc-slm' ) );
		$return_val = false;
	}

	return $return_val;
}
