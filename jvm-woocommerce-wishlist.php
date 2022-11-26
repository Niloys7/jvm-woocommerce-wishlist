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

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_jvm_woocommerce_wishlist() {

	if ( !class_exists( 'Appsero\Client' ) ) {
		require_once __DIR__ . '/appsero/src/Client.php';
	}

	$client = new Appsero\Client( '29ff6213-2aed-47b6-9bc2-f1ac982963e7', 'Wishlist for WooCommerce', __FILE__ );

	// Active insights
	$client->insights()->init();

}

appsero_init_tracker_jvm_woocommerce_wishlist();

if ( !class_exists( 'JVM_WooCommerce_Wishlist' ) ) {
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
		private $required_php_version = '7.2';

		/**
		 * @var string
		 */
		public $version = '1.3.6';

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
		 *
		 * @see WE()
		 *
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
			if ( !$this->is_woocommerce_active() ) {
				return;
			}

			if ( phpversion() < $this->required_php_version ) {
				add_action( 'admin_notices', array( $this, 'warning_php_version' ) );
				return;
			}

			$this->define_constants();
			$this->includes();
			$this->init_hooks();
			add_filter( 'plugin_row_meta', [$this, 'plugin_meta_links'], 10, 2 );

			do_action( 'wpi_woocommerce_wishlist_loaded' );
			do_action_deprecated( 'jvm_woocommerce_wishlist_loaded', [], WPI_WW_HANDOVER_VERSION, 'wpi_woocommerce_wishlist_loaded' );
		}
		
		/**
		 * Add links to plugin's description in plugins table
		 *
		 * @param array  $links Initial list of links.
		 * @param string $file  Basename of current plugin.
		 */
		function plugin_meta_links( $links, string $file ) {
			if ( $file !== plugin_basename( __FILE__ ) ) {
				return $links;
			}
			
			$support_link = '<a target="_blank" href="https://wpinteractive.com/contact-us/" title="' . __( 'Get help', 'wpgs-td' ) . '">' . __( 'Support', 'jvm-woocommerce-wishlist' ) . '</a>';
			$rate_twist   = '<a target="_blank" href="https://wordpress.org/support/plugin/jvm-woocommerce-wishlist/reviews/?filter=5"> Rate this plugin » </a>';

			
			$links[] = $support_link;
			$links[] = $rate_twist;

			return $links;
		} // plugin_meta_links
		/**
		 * Check if WooCommerce is active
		 *
		 * @see https://docs.woocommerce.com/document/create-a-plugin/
		 *
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

			add_action( 'wp_logout', array( $this, 'wp_logout' ) );
			add_action( 'wp_login', array( $this, 'wp_login' ), 10, 2 );
		}

		/**
		 * Fired on logout
		 */
		public function wp_logout() {
			$cookie_name = jvm_woocommerce_wishlist_get_site_slug() . '_wc_wishlist';

			// Set cookie to empty
			$_COOKIE[$cookie_name] = '';
			setcookie( $cookie_name, "", strtotime( "+7 day", time() ), '/' );
		}

		/**
		 * Fired on wp_login
		 */
		public function wp_login( $login, $user ) {
			$cookie_name       = jvm_woocommerce_wishlist_get_site_slug() . '_wc_wishlist';
			$cookie            = ( isset( $_COOKIE[$cookie_name] ) ) ? $_COOKIE[$cookie_name] : null;
			$user_meta         = get_user_meta( $user->ID, $cookie_name, true );
			$updated_user_meta = array();
			if ( $cookie ) {

				// Copy any cookie products from the cookie to the user meta
				$cookie_product_ids = array_unique( json_decode( '[' . $cookie . ']' ) );

				if ( !empty( $cookie_product_ids ) ) {
					$updated_user_meta = jvm_woocommerce_wishlist_clean_wishlist_product_ids( array_unique( array_merge( $user_meta, $cookie_product_ids ) ) );
					update_user_meta( $user->ID, $cookie_name, $updated_user_meta );
				}
			}

			// Update the cookie
			if ( empty( $updated_user_meta ) ) {
				setcookie( $cookie_name, jvm_woocommerce_wishlist_array_to_list( $user_meta ), strtotime( "+7 day", time() ), '/' );
			} else {
				setcookie( $cookie_name, jvm_woocommerce_wishlist_array_to_list( $updated_user_meta ), strtotime( "+7 day", time() ), '/' );
			}
		}

		/**
		 * Define WR Constants
		 */
		private function define_constants() {

			$constants = array(
				'JVM_WW_CSS'              => $this->plugin_url() . '/assets/css',
				'JVM_WW_DIR'              => $this->plugin_path(),
				'JVM_WW_JS'               => $this->plugin_url() . '/assets/js',
				'JVM_WW_IMAGES'           => $this->plugin_url() . '/assets/images',
				'JVM_WW_PATH'             => plugin_basename( __FILE__ ),
				'JVM_WW_VERSION'          => $this->version,
				'WPI_WW_HANDOVER_VERSION' => '1.3.6',
			);

			foreach ( $constants as $name => $value ) {
				$this->define( $name, $value );
			}
		}

		/**
		 * Define constant if not already set
		 * @param string      $name
		 * @param string|bool $value
		 */
		private function define( $name, $value ) {
			if ( !defined( $name ) ) {
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
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( !is_admin() || defined( 'DOING_AJAX' ) ) && !defined( 'DOING_CRON' );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {

			/**
			 * Functions used in frontend and admin
			 */
			include_once 'inc/www-core-functions.php';
			include_once 'inc/frontend/www-functions.php';

			if ( $this->is_request( 'admin' ) ) {
				include_once 'inc/admin/class-www-admin.php';
			}

			if ( $this->is_request( 'ajax' ) ) {
				include_once 'inc/ajax/www-ajax-functions.php';
			}

			if ( $this->is_request( 'frontend' ) ) {

				include_once 'inc/frontend/class-www-shortcodes.php';
			}
		}

		/**
		 * Init JVM Woocommerce Wishlist when WordPress Initialises.
		 */
		public function init() {
			// Set empty cookie if we have no cookie
			$cookie_name = jvm_woocommerce_wishlist_get_site_slug() . '_wc_wishlist';
			if ( !isset( $_COOKIE[$cookie_name] ) ) {
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
