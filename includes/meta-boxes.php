<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Save Fields
 */
function wc_slm_custom_general_fields() {
	global $woocommerce, $post;

	$post_id = $post->ID;
	$wc_slm_licensing_enabled = get_post_meta( $post_id, '_wc_slm_licensing_enabled', true ) ? true : false;
	$wc_slm_sites_allowed = esc_attr( get_post_meta($post_id, '_wc_slm_sites_allowed', true ) );
	$_wc_slm_licensing_renewal_period = esc_attr( get_post_meta( $post_id, '_wc_slm_licensing_renewal_period', true ) );
	$wc_slm_display = $wc_slm_licensing_enabled ? '' : ' style="display:none;"';

	// if nothing set so we assume lifetime!
	if ( trim( $_wc_slm_licensing_renewal_period ) == '' ) {
		$_wc_slm_licensing_renewal_period = 0;
	}
	?>
	<!-- Only show the License Key form fields if the product is downloadable -->
	<div class="options_group show_if_downloadable">
		<script type="text/javascript">
		jQuery( document ).ready( function($) {
			$( "#_wc_slm_licensing_enabled" ).on( "click",function() {
				// TODO: Improve toggle handling and prevent double display
				$( ".wc-slm-variable-toggled-hide" ).toggle();
				$( ".wc-slm-toggled-hide" ).toggle();
			} );
		} );
		</script>

		<p class="form-field">
			<input type="checkbox" name="_wc_slm_licensing_enabled" id="_wc_slm_licensing_enabled" value="1" <?php echo checked(true, $wc_slm_licensing_enabled, false); ?> />
			<label for="_wc_slm_licensing_enabled"><?php _e('Enable Software Licensing', 'wc-slm');?></label>
		</p>

		<div <?php echo $wc_slm_display; ?> class="wc-slm-toggled-hide">
			<p class="form-field">
				<label for="_wc_slm_licensing_renewal_period">
					<?php _e( 'Renewal period (Yearly)', 'wc-slm' );?>
				</label>
				<input type="number" name="_wc_slm_licensing_renewal_period" id="_wc_slm_licensing_renewal_period" value="<?php echo $_wc_slm_licensing_renewal_period; ?>"  />
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Enter the numbr of years for the Renewal Period. Enter 0 (zero) or leave blank for lifetime renewals.', 'wc-slm' );?>"></span>
			</p>
			<p class="form-field">
				<label for="_wc_slm_sites_allowed"><?php _e('Number of Sites Allowed', 'wc-slm');?></label>
				<input type="number" name="_wc_slm_sites_allowed" class="small-text" value="<?php echo $wc_slm_sites_allowed; ?>" />
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Enter the number of sites that can be activated for a single License Key. Value must be greater than 0 (zero)', 'wc-slm' );?>"></span>
			</p>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_product_options_general_product_data', 'wc_slm_custom_general_fields' );

/**
 * Save Fields
 */
function wc_slm_custom_general_fields_save( $post_id ) {
	$woocommerce_wc_slm_licensing_enabled = $_POST['_wc_slm_licensing_enabled'];
	$woocommerce_wc_slm_sites_allowed = $_POST['_wc_slm_sites_allowed'];
	$_wc_slm_licensing_renewal_period = $_POST['_wc_slm_licensing_renewal_period'];

	update_post_meta(
		$post_id,
		'_wc_slm_licensing_enabled',
		( isset( $woocommerce_wc_slm_licensing_enabled ) ? 1 : 0 )
	);

	if ( isset( $woocommerce_wc_slm_sites_allowed ) ) {
		update_post_meta(
			$post_id,
			'_wc_slm_sites_allowed',
			( absint( $woocommerce_wc_slm_sites_allowed ) >= 1 ? absint( $woocommerce_wc_slm_sites_allowed ) : 1 )
		);
	}

	update_post_meta(
		$post_id,
		'_wc_slm_licensing_renewal_period',
		( empty( $_wc_slm_licensing_renewal_period ) || absint( $_wc_slm_licensing_renewal_period ) == 0 ? 0 : absint( $_wc_slm_licensing_renewal_period ) )
	);

}
add_action( 'woocommerce_process_product_meta', 'wc_slm_custom_general_fields_save' );
