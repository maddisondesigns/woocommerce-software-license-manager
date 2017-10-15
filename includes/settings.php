<?php
/**
 * Settings
 *
 * @since       1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create the section beneath the products tab
 */
function wc_slm_section( $sections ) {

	$sections['wc_slm'] = __( 'License Manager', 'wc-slm' );
	return $sections;

}
add_filter( 'woocommerce_get_sections_products', 'wc_slm_section' );

/**
 * Add settings to the specific section we created before
 */
function wc_slm_settings( $settings, $current_section ) {

	// Check the current section is what we want
	if ( $current_section == 'wc_slm' ) {
		$settings_slm = array();
		// Add Title to the Settings
		$settings_slm[] = array(
			'name' => __( 'Software License Manager Settings', 'wc-slm' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'wcslider',
		);

		// Software License Manager API URL
		$settings_slm[] = array(
			'name' => __( 'API URL', 'wc-slm' ),
			'desc_tip' => __( 'The URL for the site that has the Software License Manager plugin installed.', 'wc-slm' ),
			'id' => 'wc_slm_api_url',
			'type' => 'text',
		);

		// Secret Key for Creation
		$settings_slm[] = array(
			'name' => __( 'Secret Key for Creation', 'wc-slm' ),
			'desc_tip' => __( 'This secret key will be used to authenticate any license creation request. This key should match the SECRET KEY FOR LICENSE CREATION key that you specified in the Software License Manager plugin settings.', 'wc-slm' ),
			'id' => 'wc_slm_api_secret',
			'type' => 'text',
			'desc' => '',
		);

		// Secret Key for Verification
		$settings_slm[] = array(
			'name' => __( 'Secret Key for Verfication', 'wc-slm' ),
			'desc_tip' => __( 'This secret key will be used to authenticate any license verfication request. This key should match the SECRET KEY FOR LICENSE VERIFICATION REQUESTS key that you specified in the Software License Manager plugin settings.', 'wc-slm' ),
			'id' => 'wc_slm_api_secret_verify',
			'type' => 'text',
			'desc' => '',
		);

		// Enable Debug Logging
		$settings_slm[] = array(
			'name' => __( 'Enable Debug Logging', 'wc-slm' ),
			'id' => 'wc_slm_debug_logging',
			'type' => 'checkbox',
			'desc' => __( 'If checked, debug messages will be written to slm_log.txt in the root of your site', 'wc-slm' ),
		);

		$settings_slm[] = array(
			'type' => 'sectionend',
			'id' => 'wcslider'
		);
		return $settings_slm;

	} else {

		// Return the standard settings
		return $settings;

	}
}
add_filter( 'woocommerce_get_settings_products', 'wc_slm_settings', 10, 2);
