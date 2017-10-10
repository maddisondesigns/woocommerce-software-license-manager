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

		// API URL Option filed
		$settings_slm[] = array(
			'name' => __( 'API URL', 'wc-slm' ),
			'desc_tip' => '',
			'id' => 'wc_slm_api_url',
			'type' => 'text',
			'desc' => __( 'Enter without http://', 'wc-slm' ),
		);

		// Secret Key
		$settings_slm[] = array(
			'name' => __( 'Secret Key', 'wc-slm' ),
			'desc_tip' => '',
			'id' => 'wc_slm_api_secret',
			'type' => 'text',
			'desc' => '',
		);

		// Secret Key
		$settings_slm[] = array(
			'name' => __( 'Enable Debug Logging', 'wc-slm' ),
			'desc_tip' => '',
			'id' => 'wc_slm_debug_logging',
			'type' => 'checkbox',
			'desc' => __( 'If checked, debug output will be written to slm_log.txt', 'wc-slm' ),
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
