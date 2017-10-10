<?php
/**
 * Helper
 *
 * @since       1.0.0
 */

// Exit if accessed directly
if ( !defined('ABSPATH') ) {
	exit;
}

/**
 * Print the passed array in a readable format
 */
function wc_slm_print_pretty( $args ) {
	echo '<pre>';
	print_r($args);
	echo '</pre>';
}

/**
 * Output a msg to the log file
 */
function wc_slm_log( $msg ) {
	$log = trailingslashit( ABSPATH ) . 'slm_log.txt';
	file_put_contents( $log, date("Y-m-d H:i:s") . ' ' . $msg . PHP_EOL, FILE_APPEND );
}

/**
 * If Logging is enabled output the msg to the log file
 */
function wc_slm_log_msg( $msg ) {
	if( !defined( 'WC_SLM_DEBUG_LOGGING' ) ) {
		return;
	}
	wc_slm_log( $msg );
}
