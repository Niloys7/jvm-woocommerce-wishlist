<?php
/**
 * Plugin Name: Wishlist for WooCommerce
 * Description: A Simple and Lightweight Wishlist for WooCommerce
 * Version: 1.3.6
 * Author: WPInteractive
 * Author URI: https://wpinteractive.com
 * Tested up to: 6.1
 * WC requires at least: 4.0
 * WC tested up to: 7.1
 * Requires PHP: 7.2
 * Stable Tag: 1.3.6
 *
 * Text Domain: jvm-woocommerce-wishlist
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
require_once __DIR__ . '/vendor/autoload.php';

define( 'CIXWW_PLUGIN_DIR', __DIR__ );
define( 'CIXWW_PLUGIN_FILE', __FILE__ );
define( 'CIXWW_PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'CIXWW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CIXWW_ASSETS', CIXWW_PLUGIN_URL . '/assets' );


/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_jvm_woocommerce_wishlist() {

	if ( ! class_exists( 'Appsero\Client' ) ) {
		require_once __DIR__ . '/appsero/src/Client.php';
	}

	$client = new Appsero\Client( '29ff6213-2aed-47b6-9bc2-f1ac982963e7', 'Wishlist for WooCommerce', __FILE__ );

	// Active insights
	$client->insights()->init();
}

appsero_init_tracker_jvm_woocommerce_wishlist();

// plugin_loaded hook
add_action(
	'plugins_loaded',
	function () {
		// VERSION 2.0
		if ( get_option( 'jvm_woocommerce_wishlist_settings_version' ) ) {
			// if page parameter is set, then redirect to settings page
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'jvm-woocommerce-wishlist-settings' ) {
				wp_redirect( admin_url( 'admin.php?page=cixwishlist_settings' ) );
				exit;
			}
			new \CIXW_WISHLIST\Bootstrap();
			require_once __DIR__ . '/inc/class-v1.php';
		} else {
			require_once __DIR__ . '/inc/class-v1.php';
			new \CIXW_WISHLIST\Settings();
		}
	},
	0
);

// options-general.php?page=jvm-woocommerce-wishlist-settings jvm_woocommerce_wishlist_settings_version
