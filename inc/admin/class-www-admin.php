<?php
/**
 * JVM Woocommerce Wishlist Admin.
 *
 * @class JVM_WW_Admin

 * @category Admin
 * @package JVMWooCommerceWishlist/Admin
 * @version 1.0.8
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * JVM_WW_Admin class.
 */
class JVM_WW_Admin {
	/**
	 * Constructor
	 */
	public function __construct() {

		// Includes files
		include_once 'class-www-options.php';

		// Admin init hooks
		$this->admin_init_hooks();
	}

	/**
	 * Perform actions on updating the theme id needed
	 */
	public function update() {

		if ( !defined( 'IFRAME_REQUEST' ) && !defined( 'DOING_AJAX' ) && ( get_option( 'jvm_woocommerce_wishlist_version' ) != JVM_WW_VERSION ) ) {
			include_once 'update.php';

			// Update hook
			do_action( 'wpi_woocommerce_wishlist_do_update' );
			do_action_deprecated( 'jvm_woocommerce_wishlist_do_update', [], WPI_WW_HANDOVER_VERSION, 'wpi_woocommerce_wishlist_do_update' );
			

			// Update version
			delete_option( 'jvm_woocommerce_wishlist_version' );
			add_option( 'jvm_woocommerce_wishlist_version', JVM_WW_VERSION );

			// After update hook
			do_action( 'jvm_woocommerce_wishlist_updated' );
		}
	}

	/**
	 * Admin init
	 */
	public function admin_init_hooks() {

		// row meta
		add_filter( 'plugin_action_links_' . plugin_basename( JVM_WW_PATH ), array( $this, 'settings_action_links' ) );

		// Update version and perform stuf if needed
		add_action( 'admin_init', array( $this, 'update' ), 0 );

		// Create page notice
		add_action( 'admin_notices', array( $this, 'check_page' ) );
		add_action( 'admin_notices', array( $this, 'create_page' ) );
	}

	/**
	 * Check albums page
	 *
	 * Display a notification if we can't get the albums page id
	 *
	 */
	public function check_page() {

		$output    = '';
		$theme_dir = get_template_directory();

		if ( get_option( 'jvm_woocommerce_wishlist_no_needs_page' ) ) {
			return;
		}

		if ( !get_option( 'jvm_woocommerce_wishlist_needs_page' ) ) {
			return;
		}

		if ( -1 == jvm_woocommerce_wishlist_get_page_id() && !isset( $_GET['jvm_woocommerce_wishlist_create_page'] ) ) {

			if ( isset( $_GET['skipjvm_woocommerce_wishlist_setup'] ) ) {
				delete_option( 'jvm_woocommerce_wishlist_needs_page' );
				return;
			}

			update_option( 'jvm_woocommerce_wishlist_needs_page', true );

			$message = '<strong>JVM Woocommerce Wishlist</strong> ' . sprintf(
				wp_kses(
					__( 'says : <em>Almost done! you need to <a href="%1$s">create a page</a> for your wishlist or <a href="%2$s">select an existing page</a> in the plugin settings</em>.', 'jvm-woocommerce-wishlist' ),
					array(
						'a'      => array(
							'href'  => array(),
							'class' => array(),
							'title' => array(),
						),
						'br'     => array(),
						'em'     => array(),
						'strong' => array(),
					)
				),
				esc_url( admin_url( '?jvm_woocommerce_wishlist_create_page=true' ) ),
				esc_url( admin_url( 'options-general.php?page=jvm-woocommerce-wishlist-settings' ) )
			);

			$message .= sprintf(
				wp_kses(
					__( '<br><br>
					<a href="%1$s" class="button button-primary">Create a page</a>
					&nbsp;
					<a href="%2$s" class="button button-primary">Select an existing page</a>
					&nbsp;
					<a href="%3$s" class="button">Skip setup</a>', 'jvm-woocommerce-wishlist' ),

					array(
						'a'      => array(
							'href'  => array(),
							'class' => array(),
							'title' => array(),
						),
						'br'     => array(),
						'em'     => array(),
						'strong' => array(),
					)
				),
				esc_url( admin_url( '?jvm_woocommerce_wishlist_create_page=true' ) ),
				esc_url( admin_url( 'options-general.php?page=jvm-woocommerce-wishlist-settings' ) ),
				esc_url( admin_url( '?skipjvm_woocommerce_wishlist_setup=true' ) )
			);

			$output = '<div class="updated jvm-woocommerce-wishlist-admin-notice jvm-woocommerce-wishlist-plugin-admin-notice"><p>';

			$output .= $message;

			$output .= '</p></div>';

			echo $output;
		} else {

			delete_option( 'jvm_woocommerce_wishlist_need_page' );
		}

		return false;
	}

	/**
	 * Create albums page
	 */
	public function create_page() {

		if ( isset( $_GET['jvm_woocommerce_wishlist_create_page'] ) && $_GET['jvm_woocommerce_wishlist_create_page'] == 'true' ) {

			$output = '';

			// Create post object
			$post = array(
				'post_title'   => esc_html__( 'Wishlist', 'jvm-woocommerce-wishlist' ),
				'post_content' => '[jvm_woocommerce_wishlist]',
				'post_type'    => 'page',
				'post_status'  => 'publish',
			);

			// Insert the post into the database
			$post_id = wp_insert_post( $post );

			if ( $post_id ) {

				update_option( 'jvm_woocommerce_wishlist_page_id', $post_id );
				update_post_meta( $post_id, '_wpb_status', 'off' ); // disable page builder mode for this page

				$message = esc_html__( 'Your wishlist page has been created succesfully', 'jvm-woocommerce-wishlist' );

				$output = '<div class="updated"><p>';

				$output .= $message;

				$output .= '</p></div>';

				echo $output;
			}

		}

		return false;
	}

	/**
	 * Add settings link in plugin page
	 */
	public function settings_action_links( $links ) {
		$setting_link = array(
			'<a href="' . admin_url( 'options-general.php?page=jvm-woocommerce-wishlist-settings' ) . '">' . esc_html__( 'Settings', 'jvm-woocommerce-wishlist' ) . '</a>',
		);
		return array_merge( $links, $setting_link );
	}
}

return new JVM_WW_Admin();