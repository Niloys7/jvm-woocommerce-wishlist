<?php
/**
 * JVM Woocommerce Wishlist Options.
 *
 * @class JVM_WW_Options
 *
 * @category Admin
 * @version 1.0.8
 * @package JVMWooCommerceWishlist/Admin
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * JVM_WW_Options class.
 */
class JVM_WW_Options {
	/**
	 * Constructor
	 */
	public function __construct() {

		// Admin init hooks
		$this->admin_init_hooks();
	}

	/**
	 * Admin init
	 */
	public function admin_init_hooks() {

		// Set default options
		add_action( 'admin_init', array( $this, 'default_options' ) );

		// Register settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add options menu
		add_action( 'admin_menu', array( $this, 'add_options_menu' ) );
	}

	/**
	 * Add options menu
	 */
	public function add_options_menu() {

		add_options_page( esc_html__( 'Wishlist', 'jvm-woocommerce-wishlist' ), esc_html__( 'Wishlist', 'jvm-woocommerce-wishlist' ), 'install_plugins', 'jvm-woocommerce-wishlist-settings', array( $this, 'options_form' ) );
	}

	/**
	 * Register options
	 */
	public function register_settings() {
		register_setting( 'jvm-woocommerce-wishlist-settings', 'jvm_woocommerce_wishlist_settings', array( $this, 'settings_validate' ) );
		add_settings_section( 'jvm-woocommerce-wishlist-settings', '', function () {}, 'jvm-woocommerce-wishlist-settings' );
		add_settings_field( 'page_id', esc_html__( 'Wishlist Page', 'jvm-woocommerce-wishlist' ), array( $this, 'setting_page_id' ), 'jvm-woocommerce-wishlist-settings', 'jvm-woocommerce-wishlist-settings' );
		add_settings_field( 'instructions', esc_html__( 'Instructions', 'jvm-woocommerce-wishlist' ), array( $this, 'setting_instructions' ), 'jvm-woocommerce-wishlist-settings', 'jvm-woocommerce-wishlist-settings' );

	}

	/**
	 * Validate options
	 *
	 * @param  array $input
	 * @return array $input
	 */
	public function settings_validate( $input ) {

		if ( isset( $input['page_id'] ) ) {
			update_option( 'jvm_woocommerce_wishlist_page_id', intval( $input['page_id'] ) );
			unset( $input['page_id'] );
		}

		return $input;
	}

	/**
	 * Page settings
	 *
	 * @return string
	 */
	public function setting_page_id() {
		$pages = get_pages();
		?>
		<select name="jvm_woocommerce_wishlist_settings[page_id]">
			<option value="-1"><?php esc_html_e( 'Select a page...', 'jvm-woocommerce-wishlist' );?></option>
			<?php foreach ( $pages as $page ): ?>
				<option <?php selected( absint( $page->ID ), get_option( 'jvm_woocommerce_wishlist_page_id' ) );?> value="<?php echo intval( $page->ID ); ?>"><?php echo sanitize_text_field( $page->post_title ); ?></option>
			<?php endforeach;?>
		</select>
		<p>
			<label for="jvm_woocommerce_wishlist_settings[page_id]">
				<?php printf( esc_html__( 'It is recommended to set your wishlist page here, so themes and plugins can access its URL with the %s function.', 'jvm-woocommerce-wishlist' ), 'jvm_get_wishlist_url()' );?>
			</label>
		</p>
		<?php
}

	/**
	 * Display additional instructions
	 */
	public function setting_instructions() {
		?>
		<p><?php printf( esc_html__( 'You can use the %s shortcode to display your wishlist in your page.', 'jvm-woocommerce-wishlist' ), '[jvm_woocommerce_wishlist]' )?></p>
		<p>Anything you are missing in this plugin? Submit your idea <a target="_blank" href="https://app.loopedin.io/wishlist-for-woocommerce#/ideas">here</a></p>
		<?php
}

	/**
	 * Options form
	 */
	public function options_form() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Wishlist Options', 'jvm-woocommerce-wishlist' );?></h2>
			<form action="options.php" method="post">
				<?php settings_fields( 'jvm-woocommerce-wishlist-settings' );?>
				<?php do_settings_sections( 'jvm-woocommerce-wishlist-settings' );?>
				<p class="submit"><input name="save" type="submit" class="button-primary" value="<?php esc_html_e( 'Save Changes', 'jvm-woocommerce-wishlist' );?>" /></p>
			</form>
		</div>
		<?php
}

	/**
	 * Set default options
	 */
	public function default_options() {

		// delete_option( 'jvm_woocommerce_wishlist_settings' );

		if ( false === get_option( 'jvm_woocommerce_wishlist_settings' ) ) {

			$default = array(

			);

			add_option( 'jvm_woocommerce_wishlist_settings', $default );
		}
	}
} // end class

return new JVM_WW_Options();