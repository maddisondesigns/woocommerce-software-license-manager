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
			<span class="description"><?php _e( 'Enter 0 (zero) for lifetime renewals.', 'wc-slm' );?></span>
		</p>
		<p class="form-field">
			<label for="_wc_slm_sites_allowed"><?php _e('Number of Sites Allowed', 'wc-slm');?></label>
			<input type="number" name="_wc_slm_sites_allowed" class="small-text" value="<?php echo $wc_slm_sites_allowed; ?>" />
			<span class="description"><?php _e( 'Enter the number of sites that can be activated for a single License Key.', 'wc-slm' );?></span>
		</p>
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

	if ( !empty( $woocommerce_wc_slm_licensing_enabled ) ) {
		update_post_meta( $post_id, '_wc_slm_licensing_enabled', esc_html( $woocommerce_wc_slm_licensing_enabled ) );
	}

	if ( !empty( $woocommerce_wc_slm_sites_allowed ) ) {
		update_post_meta( $post_id, '_wc_slm_sites_allowed', esc_html( $woocommerce_wc_slm_sites_allowed ) );
	}

	if ( !empty( $_wc_slm_licensing_renewal_period ) ) {
		update_post_meta( $post_id, '_wc_slm_licensing_renewal_period', esc_html( $_wc_slm_licensing_renewal_period ) );
	}

}
add_action( 'woocommerce_process_product_meta', 'wc_slm_custom_general_fields_save' );
