<?php
/**
 * Plugin Name: JVM WooCommerce Wishlist
 * Description: A Simple and Lightweight Wishlist for WooCommerce
 * Version: 1.3.4
 * Author: Joris van Montfort
 * Author URI: http://www.jorisvm.nl
 * Requires at least: 4.4.1
 * Tested up to: 5.4.1
 *
 * Text Domain: jvm-woocommerce-wishlist
 * Domain Path: /languages/
 *
 * This WordPress Plugin is a free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * It is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * See https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'JVM_WooCommerce_Wishlist' ) ) {
	/**
	 * Main JVM_WooCommerce_Wishlist Class
	 *
	 * Contains the main functions for JVM_WooCommerce_Wishlist
	 *
	 * @class JVM_WooCommerce_Wishlist
	 */
	class JVM_WooCommerce_Wishlist {

		/**
		 * @var string
		 */
		private $required_php_version = '5.4.0';

		/**
		 * @var string
		 */
		public $version = '1.3.4';

		/**
		 * @var JVM Woocommerce Wishlist The single instance of the class
		 */
		protected static $_instance = null;

		/**
		 * @var string
		 */
		public $template_url;

		/**
		 * Main JVM Woocommerce Wishlist Instance
		 *
		 * Ensures only one instance of JVM Woocommerce Wishlist is loaded or can be loaded.
		 *
		 * @static
		 * @see WE()
		 * @return JVM Woocommerce Wishlist - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheating huh?', 'jvm-woocommerce-wishlist' ), '1.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheating huh?', 'jvm-woocommerce-wishlist' ), '1.0' );
		}

		/**
		 * JVM Woocommerce Wishlist Constructor.
		 */
		public function __construct() {

			/* Don't do anything if WC is not activated */
			if ( ! $this->is_woocommerce_active() ) {
				return;
			}


			if ( phpversion() < $this->required_php_version ) {
				add_action( 'admin_notices', array( $this, 'warning_php_version' ) );
				return;
			}

			$this->define_constants();
			$this->includes();
			$this->init_hooks();

			do_action( 'jvm_woocommerce_wishlist_loaded' );
		}

		/**
		 * Check if WooCommerce is active
		 *
		 * @see https://docs.woocommerce.com/document/create-a-plugin/
		 * @return bool
		 */
		public function is_woocommerce_active() {
			return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
		}

		/**
		 * Display error notice if PHP version is too low
		 */
		public function warning_php_version() {
			?>
			<div class="notice notice-error">
				<p><?php

				printf(
					esc_html__( '%1$s needs at least PHP %2$s installed on your server. You have version %3$s currently installed. Please contact your hosting service provider if you\'re not able to update PHP by yourself.', 'jvm-woocommerce-wishlist' ),
					'JVM Woocommerce Wishlist',
					$this->required_php_version,
					phpversion()
				);
				?></p>
			</div>
			<?php
		}

		/**
		 * Hook into actions and filters
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'init' ), 0 );

			add_action('wp_logout', array($this, 'wp_logout'));
			add_action('wp_login', array($this, 'wp_login'), 10, 2);
		}

		/**
		 * Fired on logout
		 */
		public function wp_logout() {
			$cookie_name = jvm_woocommerce_wishlist_get_site_slug() . '_wc_wishlist';

			// Set cookie to empty
			$_COOKIE[$cookie_name] = '';
			setcookie($cookie_name, "",  strtotime("+7 day", time()), '/');
		}

		/**
		 * Fired on wp_login
		 */
		public function wp_login($login, $user) {
			$cookie_name = jvm_woocommerce_wishlist_get_site_slug() . '_wc_wishlist';
			$cookie = ( isset( $_COOKIE[ $cookie_name ] ) ) ? $_COOKIE[ $cookie_name ] : null;
			$user_meta = get_user_meta( $user->ID , $cookie_name, true );
			$updated_user_meta = array();
			if ($cookie) {
				
				// Copy any cookie products from the cookie to the user meta
				$cookie_product_ids = array_unique( json_decode( '[' . $cookie . ']' ) );

				if (!empty($cookie_product_ids)) {
					$updated_user_meta = jvm_woocommerce_wishlist_clean_wishlist_product_ids(array_unique(array_merge($user_meta, $cookie_product_ids)));
					update_user_meta( $user->ID, $cookie_name, $updated_user_meta );
				}
			}

			// Update the cookie
			if (empty($updated_user_meta)) {
				setcookie($cookie_name, jvm_woocommerce_wishlist_array_to_list($user_meta),  strtotime("+7 day", time()), '/');
			}else {
				setcookie($cookie_name, jvm_woocommerce_wishlist_array_to_list($updated_user_meta),  strtotime("+7 day", time()), '/');
			}
		}

		/**
		 * Activation function
		 */
		public function activate() {

			add_option( 'jvm_woocommerce_wishlist_needs_page', true );
		}

		/**
		 * Define WR Constants
		 */
		private function define_constants() {

			$constants = array(
				'JVM_WW_CSS' => $this->plugin_url() . '/assets/css',
				'JVM_WW_DIR' => $this->plugin_path(),
				'JVM_WW_JS' => $this->plugin_url() . '/assets/js',
				'JVM_WW_IMAGES' => $this->plugin_url() . '/assets/images',
				'JVM_WW_PATH' => plugin_basename( __FILE__ ),
				'JVM_WW_VERSION' => $this->version,
			);

			foreach ( $constants as $name => $value ) {
				$this->define( $name, $value );
			}
		}

		/**
		 * Define constant if not already set
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 * string $type ajax, frontend or admin
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {

			/**
			 * Functions used in frontend and admin
			 */
			include_once( 'inc/www-core-functions.php' );

			if ( $this->is_request( 'admin' ) ) {
				include_once( 'inc/admin/class-www-admin.php' );
			}

			if ( $this->is_request( 'ajax' ) ) {
				include_once( 'inc/ajax/www-ajax-functions.php' );
			}

			if ( $this->is_request( 'frontend' ) ) {
				include_once( 'inc/frontend/www-functions.php' );
				include_once( 'inc/frontend/class-www-shortcodes.php' );
			}
		}

		/**
		 * Init JVM Woocommerce Wishlist when WordPress Initialises.
		 */
		public function init() {
			// Set empty cookie if we have no cookie
			$cookie_name = jvm_woocommerce_wishlist_get_site_slug() . '_wc_wishlist';
			if (!isset($_COOKIE[$cookie_name])) {
				$_COOKIE[$cookie_name] = '';
				//setcookie($cookie_name, "",  strtotime("+7 day", time()), '/');
			}

			// Set up localisation
			$this->load_plugin_textdomain();
		}

		/**
		 * Loads the plugin text domain for translation
		 */
		public function load_plugin_textdomain() {

			$domain = 'jvm-woocommerce-wishlist';
			$locale = apply_filters( 'jvm-woocommerce-wishlist', get_locale(), $domain );
			load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Get the plugin url.
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get the template path.
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'jvm_woocommerce_wishlist_template_path', 'jvm-woocommerce-wishlist/' );
		}
	} // end class
} // end class check


// GO!
JVM_WooCommerce_Wishlist::instance();