<?php
namespace CIXW_WISHLIST;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Settings {
	public function __construct() {
		$this->pluginOptions();
		add_action( 'csf_cixwishlist_settings_save_after', array( $this, 'save_after' ) );
	}
	public function save_after( $data ) {
	}
	public function pluginOptions() {

		// Set a unique slug-like ID
		$prefix = 'cixwishlist_settings';

		//
		// Create options
		\CSF::createOptions(
			$prefix,
			array(
				'menu_title'      => 'Wishlist Settings',
				'menu_slug'       => $prefix,
				'framework_title' => 'Wishlist for WooCommerce Settings <small>v</small>',
				'menu_type'       => 'submenu',
				'menu_parent'     => apply_filters( 'ciwishlist_menu_parent', 'codeixer' ),
				// 'nav'             => 'tab',
				// 'theme'           => 'light',
				'footer_text'     => '',
				// menu extras
				'show_bar_menu'   => false,
				'show_footer'     => false,
				'ajax_save'       => false,

			)
		);

		// Create General section
		\CSF::createSection(
			$prefix,
			array(
				'title'  => 'General Settings',
				'icon'   => 'fas fa-cog',
				'fields' => array(

					// A text field
					array(
						'id'      => 'wishlist_name',
						'type'    => 'text',

						'title'   => __( 'Default Wishlist Name', 'jvm-woocommerce-wishlist' ),
						'default' => 'Wishlist',
					),

					array(
						'id'          => 'wishlist_page',
						'type'        => 'select',
						'title'       => __( 'Wishlist Page', 'jvm-woocommerce-wishlist' ),
						'placeholder' => 'Select a page',
						'chosen'      => true,
						'ajax'        => true,
						'default'     => 'option-2',
						'options'     => 'pages',
						'width'       => '250px',
						'class'       => 'default-wishlist-page-field',
						'desc'        => '<style>.default-wishlist-page-field .chosen-container {width: 445px !important;}</style>',
					),
					// add switcher for Require Login
					array(
						'id'      => 'wishlist_require_login',
						'type'    => 'switcher',
						'title'   => __( 'Require Login', 'jvm-woocommerce-wishlist' ),
						'default' => false,
						'desc'   => __( 'Require users to be logged in to add items to the wishlist.', 'jvm-woocommerce-wishlist' ),
					),
					
					array(
						'id'      => 'remove_on_second_click',
						'type'    => 'switcher',
						'title'   => __( 'Remove product from Wishlist on the second click', 'jvm-woocommerce-wishlist' ),
						'default' => false,
						'desc'   => __( 'Remove product from Wishlist on the second click.', 'jvm-woocommerce-wishlist' ),
					),

				),
			)
		);

		// Create a section
		\CSF::createSection(
			$prefix,
			array(
				'title'  => __( 'Add To Wishlist Button', 'jvm-woocommerce-wishlist' ), // It will be displayed in the title bar
				'icon'   => 'fas fa-cog',
				'fields' => array(
					// add switcher field for loop settings
					array(
						'id'      => 'loop_button',
						'type'    => 'switcher',
						'title'   => __( 'Display "Add to Wishlist" in loop', 'jvm-woocommerce-wishlist' ),
						'desc'   => __( 'Display "Add to Wishlist" button on product listings like Shop page, categories, etc.', 'jvm-woocommerce-wishlist' ),
						'default' => true,

					),
					array(
						'id'         => 'loop_button_position',
						'type'       => 'select',
						'title'      => __( '"Add to Wishlist" Position', 'jvm-woocommerce-wishlist' ),
						'options'    => array(
							'after'    => __( 'After "Add to Cart" button', 'jvm-woocommerce-wishlist' ),
							'before'   => __( 'Before "Add to Cart" button', 'jvm-woocommerce-wishlist' ),
							'in_thumb' => __( 'Above Thumbnail', 'jvm-woocommerce-wishlist' ),
							'custom'   => __( 'Custom Position / Shortcode', 'jvm-woocommerce-wishlist' ),

						),
						'default'    => 'woocommerce_after_single_product_summary',
						'dependency' => array( 'loop_button', '==', 'true' ),
					),
					array(
						'id'      => 'product_button',
						'type'    => 'switcher',
						'title'   => __( 'Display "Add to Wishlist" in single product', 'jvm-woocommerce-wishlist' ),
						'default' => true,

					),
					// select field for button position
					array(
						'id'         => 'product_button_position',
						'type'       => 'select',
						'title'      => __( '"Add to Wishlist" Position', 'jvm-woocommerce-wishlist' ),
						'options'    => array(
							'after'    => __( 'After "Add to Cart" button', 'jvm-woocommerce-wishlist' ),
							'before'   => __( 'Before "Add to Cart" button', 'jvm-woocommerce-wishlist' ),
							'after_summary' => __( 'After Summary', 'jvm-woocommerce-wishlist' ),
							'custom'   => __( 'Custom Position / Shortcode', 'jvm-woocommerce-wishlist' ),
						),
						'default'    => 'woocommerce_after_single_product_summary',
						'dependency' => array( 'product_button', '==', 'true' ),
						'desc' 	 => __( 'Select the position where you want to display "Add to Wishlist" button on the single product page', 'jvm-woocommerce-wishlist' ),
					),
					// select field for button type
					array(
						'id'      => 'product_button_type',
						'type'    => 'select',
						'title'   => __( 'Button Type', 'jvm-woocommerce-wishlist' ),
						'options' => array(
							'button' => 'Button',
							'link'   => 'Link',
						),
						'default' => 'button',
					),
					// button icon switcher
					array(
						'id'      => 'product_button_icon',
						'type'    => 'switcher',
						'title'   => __( 'Button Icon', 'jvm-woocommerce-wishlist' ),
						'default' => true,
					),
					// icon color field
					array(
						'id'         => 'product_button_icon_color',
						'type'       => 'color',
						'title'      => __( 'Icon Color', 'jvm-woocommerce-wishlist' ),
						'default'    => '#000000',
						'dependency' => array( 'product_button_icon', '==', 'true' ),
					),
					// wisth button text field
					array(
						'id'      => 'product_button_text',
						'type'    => 'text',
						'title'   => __( 'Button Text', 'jvm-woocommerce-wishlist' ),
						'default' => 'Add to Wishlist',
					),
					// add text field for Remove from Wishlist
					array(
						'id'      => 'product_button_remove_text',
						'type'    => 'text',
						'title'   => __( '"Remove from Wishlist" Text', 'jvm-woocommerce-wishlist' ),
						'default' => 'Remove from Wishlist',
					),
					// view wishlist text field
					array(
						'id'      => 'product_view_wishlist_text',
						'type'    => 'text',
						'title'   => __( 'View Wishlist Text', 'jvm-woocommerce-wishlist' ),
						'default' => 'View Wishlist',
					),
					// prodct already in wishlist text field
					array(
						'id'      => 'product_already_in_wishlist_text',
						'type'    => 'text',
						'title'   => __( 'Product Already in Wishlist Text', 'jvm-woocommerce-wishlist' ),
						'default' => 'Product Already in Wishlist',
					),
					// product added to wishlist text field
					array(
						'id'      => 'product_added_to_wishlist_text',
						'type'    => 'text',
						'title'   => __( 'Product Added to Wishlist Text', 'jvm-woocommerce-wishlist' ),
						'default' => 'Product Added to Wishlist',
					),
					// product removed from wishlist text field
					array(
						'id'      => 'product_removed_from_wishlist_text',
						'type'    => 'text',
						'title'   => __( 'Product Removed from Wishlist Text', 'jvm-woocommerce-wishlist' ),
						'default' => 'Product Removed from Wishlist',
					),
					// redirect to wishlist page switcher
					array(
						'id'      => 'product_redirect_to_wishlist',
						'type'    => 'switcher',
						'title'   => __( 'Redirect to Wishlist Page', 'jvm-woocommerce-wishlist' ),
						'default' => true,
					),
					// add text field with no edit
					array(
						'id'         => 'product_add_to_wishlist_text',
						'type'       => 'text',
						'title'      => __( 'Add to Wishlist Text', 'jvm-woocommerce-wishlist' ),
						'default'    => '[cix_add_to_wishlist product_id="%pid%"]',
						'attributes' => array(
							'readonly' => 'readonly',
						),
					),

				),
			)
		);
		// Create wishlist page section
		\CSF::createSection(
			$prefix,
			array(
				'title'  => 'Wishlist Page',
				'icon'   => 'fas fa-cog',
				'fields' => array(

					// wishlist page no item text field
					array(
						'id'      => 'wishlist_page_no_item_text',
						'type'    => 'text',
						'title'   => __( 'No Item Text', 'jvm-woocommerce-wishlist' ),
						'default' => 'No items in your wishlist',
					),

					// wishlist page table add to cart text field
					array(
						'id'      => 'wishlist_page_table_add_to_cart_text',
						'type'    => 'text',
						'title'   => __( 'Add to Cart Text', 'jvm-woocommerce-wishlist' ),
						'default' => 'Add to Cart',
					),

					array(
						'id'      => 'wishlist_page_table_unit_price',
						'type'    => 'switcher',
						'title'   => __( 'Show Unit Price', 'jvm-woocommerce-wishlist' ),
						'default' => true,
					),
					// add switcher for stock status
					array(
						'id'      => 'wishlist_page_table_stock_status',
						'type'    => 'switcher',
						'title'   => __( 'Show Stock Status', 'jvm-woocommerce-wishlist' ),
						'default' => true,
					),
					// add switcher for quantity
					array(
						'id'      => 'wishlist_page_table_quantity',
						'type'    => 'switcher',
						'title'   => __( 'Show Quantity', 'jvm-woocommerce-wishlist' ),
						'default' => true,
					),
					// add switcher for total price
					array(
						'id'      => 'wishlist_page_table_total_price',
						'type'    => 'switcher',
						'title'   => __( 'Show Total Price', 'jvm-woocommerce-wishlist' ),
						'default' => true,
					),

					// add switcher for added date
					array(
						'id'      => 'wishlist_page_table_added_date',
						'type'    => 'switcher',
						'title'   => __( 'Show Added Date', 'jvm-woocommerce-wishlist' ),
						'default' => true,
					),
					// add switcher for show checkbox
					array(
						'id'      => 'wishlist_page_table_checkbox',
						'type'    => 'switcher',
						'title'   => __( 'Show Checkbox', 'jvm-woocommerce-wishlist' ),
						'default' => true,
					),
					// add switcher for redirect to cart
					array(
						'id'      => 'wishlist_page_table_redirect_to_cart',
						'type'    => 'switcher',
						'title'   => __( 'Redirect to Cart', 'jvm-woocommerce-wishlist' ),
						'desc'    => __( 'Redirect to cart page after adding to cart from wishlist page.', 'jvm-woocommerce-wishlist' ),
						'default' => true,
					),
					// add switcher for remove if added to cart
					array(
						'id'      => 'wishlist_page_table_remove_if_added_to_cart',
						'type'    => 'switcher',
						'title'   => __( 'Remove if Added to Cart', 'jvm-woocommerce-wishlist' ),
						'desc'    => __( 'Remove item from wishlist if added to cart.', 'jvm-woocommerce-wishlist' ),
						'default' => true,
					),
					// add switcher for "Add All to Cart" button
					array(
						'id'      => 'table_add_all_to_cart',
						'type'    => 'switcher',
						'title'   => __( 'Show "Add All to Cart" Button', 'jvm-woocommerce-wishlist' ),
						'default' => true,
					),
					// add text field for "Add All to Cart" button text
					array(
						'id'         => 'table_add_all_to_cart_text',
						'type'       => 'text',
						'title'      => __( '"Add All to Cart" Button Text', 'jvm-woocommerce-wishlist' ),
						'default'    => 'Add All to Cart',
						'dependency' => array( 'table_add_all_to_cart', '==', 'true' ),
					),

				),
			)
		);
		// add section for Advanced Settings
		\CSF::createSection(
			$prefix,
			array(
				'title'  => 'Advanced Settings',
				'icon'   => 'fas fa-cog',
				'fields' => array(
					// add css field
					array(
						'id'       => 'wishlist_css',
						'type'     => 'code_editor',
						'title'    => __( 'Custom CSS', 'jvm-woocommerce-wishlist' ),
						'default'  => '',
						'settings' => array(
							'theme' => 'mbo',
							'mode'  => 'css',
						),
					),
				),
			)
		);

					// add switcher for disable cache

		// TODO: move to pro version
		// License key
		\CSF::createSection(
			$prefix,
			array(
				'title'  => __( 'License', 'deposits-for-woocommerce' ),
				'icon'   => 'fas fa-key',
				'fields' => array(

					// A Callback Field Example
					array(
						'id'          => 'license-key',
						'type'        => 'text',
						'title'       => __( 'Purchase Code', 'deposits-for-woocommerce' ),
						'placeholder' => __( 'Enter Purchase Code', 'deposits-for-woocommerce' ),
						'desc'        => __( 'Enter your license key here, to activate <strong>Bayna - Deposits for WooCommerce PRO</strong>, and get automatic updates and premium support. <a href="' . apply_filters( 'bayna_learn_more', 'https://www.codeixer.com/docs/where-is-my-purchase-code/' ) . '" target="_blank">Learn More</a>', 'deposits-for-woocommerce' ),
					),
					array(
						'type'     => 'callback',
						'function' => 'wcbaynaLicense',
					),

				),
			)
		);
	}

	/**
	 * Delete all '$preifx' transients from the database.
	 */
	public static function delete_transients( $prefix ) {
		$pf = new self();
		$pf->delete_transients_with_prefix( $prefix );
	}
	/**
	 * Delete all transients from the database whose keys have a specific prefix.
	 *
	 * @param string $prefix The prefix. Example: 'my_cool_transient_'.
	 */
	public function delete_transients_with_prefix( $prefix ) {
		foreach ( $this->get_transient_keys_with_prefix( $prefix ) as $key ) {
			delete_transient( $key );
		}
	}
	/**
	 * Gets all transient keys in the database with a specific prefix.
	 *
	 * Note that this doesn't work for sites that use a persistent object
	 * cache, since in that case, transients are stored in memory.
	 *
	 * @param  string $prefix Prefix to search for.
	 * @return array          Transient keys with prefix, or empty array on error.
	 */
	private function get_transient_keys_with_prefix( $prefix ) {
		global $wpdb;

		$prefix = $wpdb->esc_like( '_transient_' . $prefix );
		$sql    = "SELECT `option_name` FROM $wpdb->options WHERE `option_name` LIKE '%s'";
		$keys   = $wpdb->get_results( $wpdb->prepare( $sql, $prefix . '%' ), ARRAY_A );

		if ( is_wp_error( $keys ) ) {
			return array();
		}

		return array_map(
			function ( $key ) {
				// Remove '_transient_' from the option name.
				return substr( $key['option_name'], strlen( '_transient_' ) );
			},
			$keys
		);
	}
}
