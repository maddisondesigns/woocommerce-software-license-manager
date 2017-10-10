<?php
/**
 * Plugin Name:     WC Software License Manager
 * Plugin URI:      http://wp-master.ir
 * Description:     Seamless integration between Woocommerce and Software License Manager(adopted from EDD Software License Manager -thanks to flowdee <coder@flowdee.de>)
 * Version:         1.0.7
 * Author:          Omid Shamlu
 * Author URI:      http://wp-master.ir
 * Text Domain:     wc-slm
 * Domain Path:     /languages
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 3, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @author          Omid <wp@wp-master.ir>
 * @copyright       Copyright (c) Omid
 * @license         http://www.gnu.org/licenses/gpl-3.0.html
 *
 * TODO:
 * https://wordpress.org/support/topic/modifying-for-variable-products
 * Add option to recreate manauall linense in order edit page
 * Add license columns in order table lists
 * log to Actions (Denug purpose)
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('WC_SLM')) {

	/**
	 * Main WC_SLM class
	 *
	 * @since       1.0.0
	 */
	class WC_SLM {

		/**
		 * @var         WC_SLM $instance The one true WC_SLM
		 * @since       1.0.0
		 */
		private static $instance;

		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      object self::$instance The one true WC_SLM
		 */
		public static function instance() {
			if (!self::$instance) {
				self::$instance = new WC_SLM();
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
			}

			return self::$instance;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function setup_constants() {

			// Plugin version
			define('WC_SLM_VER', '1.0.0');

			// Plugin path
			define('WC_SLM_DIR', plugin_dir_path(__FILE__));

			// Plugin URL
			define('WC_SLM_URL', plugin_dir_url(__FILE__));

			// SLM Credentials
			$api_url = str_replace(array('http://'), array('https://'), rtrim(get_option('wc_slm_api_url'), '/'));

			define('WC_SLM_API_URL', $api_url);
			define('WC_SLM_API_SECRET', get_option('wc_slm_api_secret'));
		}

		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function includes() {

			// Get out if WC is not active
			if (!function_exists('WC')) {
				return;
			}

			// Include files and scripts
			require_once WC_SLM_DIR . 'includes/helper.php';

			if (is_admin()) {
				require_once WC_SLM_DIR . 'includes/meta-boxes.php';
				require_once WC_SLM_DIR . 'includes/settings.php';
			}

			require_once WC_SLM_DIR . 'includes/emails.php';
			require_once WC_SLM_DIR . 'includes/purchase.php';
		}

		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      void
		 */
		public function load_textdomain() {

			// Load the default language files
			load_plugin_textdomain('wc-slm', false, 'wc-software-license-manager/languages');
			__('Seamless integration between Woocommerce and Software License Manager(adopted from EDD Software License Manager -thanks to flowdee <coder@flowdee.de>)', 'wc-slm');
			__('WC Software License Manager', 'wc-slm');
		}

		/*
			         * Activation function fires when the plugin is activated.
			         *
			         * @since  1.0.0
			         * @access public
			         * @return void
		*/
		public static function activation() {
			// nothing
		}

		/*
			         * Uninstall function fires when the plugin is being uninstalled.
			         *
			         * @since  1.0.0
			         * @access public
			         * @return void
		*/
		public static function uninstall() {
			// nothing
		}
	}

	/**
	 * The main function responsible for returning the one true WC_SLM
	 * instance to functions everywhere
	 *
	 * @since       1.0.0
	 * @return      \WC_SLM The one true WC_SLM
	 */
	function WC_SLM_load() {

		return WC_SLM::instance();
	}

	/**
	 * The activation & uninstall hooks are called outside of the singleton because WordPress doesn't
	 * register the call from within the class hence, needs to be called outside and the
	 * function also needs to be static.
	 */
	register_activation_hook(__FILE__, array('WC_SLM', 'activation'));
	register_uninstall_hook(__FILE__, array('WC_SLM', 'uninstall'));

	add_action('plugins_loaded', 'WC_SLM_load');

} // End if class_exists check