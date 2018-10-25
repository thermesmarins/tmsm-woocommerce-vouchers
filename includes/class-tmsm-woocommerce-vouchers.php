<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/thermesmarins/
 * @since      1.0.0
 *
 * @package    Tmsm_Woocommerce_Vouchers
 * @subpackage Tmsm_Woocommerce_Vouchers/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tmsm_Woocommerce_Vouchers
 * @subpackage Tmsm_Woocommerce_Vouchers/includes
 * @author     Nicolas Mollet <nmollet@thalassotherapie.com>
 */
class Tmsm_Woocommerce_Vouchers {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tmsm_Woocommerce_Vouchers_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'tmsm-woocommerce-vouchers';
		$this->version = '1.1.7';

		$this->load_dependencies();
		$this->set_locale();
		$this->register_post_types();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Tmsm_Woocommerce_Vouchers_Loader. Orchestrates the hooks of the plugin.
	 * - Tmsm_Woocommerce_Vouchers_i18n. Defines internationalization functionality.
	 * - Tmsm_Woocommerce_Vouchers_Admin. Defines all hooks for the admin area.
	 * - Tmsm_Woocommerce_Vouchers_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tmsm-woocommerce-vouchers-loader.php';

		/**
		 * The class responsible for defining post types
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tmsm-woocommerce-vouchers-posttypes.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tmsm-woocommerce-vouchers-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tmsm-woocommerce-vouchers-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tmsm-woocommerce-vouchers-public.php';

		$this->loader = new Tmsm_Woocommerce_Vouchers_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Tmsm_Woocommerce_Vouchers_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function register_post_types() {

		$plugin_posttypes = new Tmsm_Woocommerce_Vouchers_Posttypes();

		$this->loader->add_filter( 'init', $plugin_posttypes, 'register_post_type_localbusiness' );

		$this->loader->add_filter( 'acf/settings/path', $plugin_posttypes, 'acf_path' );
		$this->loader->add_filter( 'acf/settings/dir', $plugin_posttypes, 'acf_dir' );
		$this->loader->add_filter( 'plugins_loaded', $plugin_posttypes, 'acf_setup' );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Tmsm_Woocommerce_Vouchers_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Tmsm_Woocommerce_Vouchers_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Tmsm_Woocommerce_Vouchers_Admin( $this->get_plugin_name(), $this->get_version() );




		// Scripts & Styles
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'plugin_action_links_' . plugin_basename( TMSMWOOCOMMERCEVOUCHERS_PLUGINDIR . 'tmsm-woocommerce-vouchers.php'), $plugin_admin, 'plugin_action_links' );

		$this->loader->add_action( 'save_post', $plugin_admin, 'save_post', 10, 1 );

		// Product types
		$this->loader->add_filter( 'woocommerce_product_data_tabs', $plugin_admin, 'woocommerce_product_data_tabs_voucher', 98 );
		$this->loader->add_filter( 'woocommerce_product_data_panels', $plugin_admin, 'woocommerce_product_data_panels_voucher' );
		$this->loader->add_action( 'woocommerce_process_product_meta_simple', $plugin_admin, 'woocommerce_process_product_save_voucher_options' );
		$this->loader->add_action( 'woocommerce_process_product_meta_variable', $plugin_admin, 'woocommerce_process_product_save_voucher_options' );
		$this->loader->add_filter( 'product_type_options', $plugin_admin, 'woocommerce_product_type_options_voucher' );
		$this->loader->add_action( 'woocommerce_variation_options', $plugin_admin, 'woocommerce_variation_options_voucher', 10 , 3 );
		$this->loader->add_action( 'woocommerce_save_product_variation', $plugin_admin, 'woocommerce_save_product_variation_voucher', 10, 2 );

		// WooCommerce settings
		$this->loader->add_filter( 'woocommerce_get_settings_pages', $plugin_admin, 'woocommerce_get_settings_pages_vouchers' );
		//$this->loader->add_filter( 'woocommerce_settings_tabs_array', $plugin_admin, 'woocommerce_settings_tabs_array_vouchers', 20 );
		//$this->loader->add_filter( 'woocommerce_settings_vouchers', $plugin_admin, 'woocommerce_settings_vouchers' );
		//$this->loader->add_filter( 'woocommerce_settings_save_vouchers', $plugin_admin, 'woocommerce_settings_save_vouchers' );
		//$this->loader->add_filter( 'woocommerce_sections_vouchers', $plugin_admin, 'woocommerce_sections_vouchers' );

		// Order
		$this->loader->add_action( 'woocommerce_hidden_order_itemmeta', $plugin_admin, 'woocommerce_hidden_order_itemmeta', 10, 1 );
		$this->loader->add_action( 'woocommerce_order_item_get_formatted_meta_data', $plugin_admin, 'woocommerce_order_item_get_formatted_meta_data', 10, 2 );

		// Virtual only Column
		$this->loader->add_action( 'manage_shop_order_posts_custom_column', $plugin_admin, 'shop_order_posts_custom_column_virtualonly', 50, 2 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Tmsm_Woocommerce_Vouchers_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'woocommerce_before_add_to_cart_button', $plugin_public, 'woocommerce_before_add_to_cart_button' );
		$this->loader->add_filter( 'woocommerce_add_to_cart_validation', $plugin_public, 'woocommerce_add_to_cart_validation', 10, 6 );
		$this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_public, 'woocommerce_add_cart_item_data', 10, 3 );
		$this->loader->add_filter( 'woocommerce_get_item_data', $plugin_public, 'woocommerce_get_item_data', 10, 2 );
		$this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $plugin_public, 'woocommerce_checkout_create_order_line_item', 10, 4 );
		$this->loader->add_filter( 'woocommerce_display_item_meta', $plugin_public, 'woocommerce_display_item_meta', 10, 3 );
		$this->loader->add_action( 'woocommerce_grant_product_download_permissions', $plugin_public, 'woocommerce_grant_product_download_permissions', 10, 1 );
		$this->loader->add_action( 'woocommerce_get_item_downloads', $plugin_public, 'woocommerce_get_item_downloads', 10, 3 );
		$this->loader->add_filter( 'woocommerce_customer_get_downloadable_products', $plugin_public, 'woocommerce_customer_get_downloadable_products', 10, 1 );
		$this->loader->add_action( 'woocommerce_download_product', $plugin_public, 'woocommerce_download_product', 10, 6 );
		$this->loader->add_filter( 'woocommerce_email_attachments', $plugin_public, 'woocommerce_email_attachments', 10, 3 );

		$this->loader->add_action('woocommerce_payment_complete', $plugin_public, 'woocommerce_payment_complete', 10, 1);
		$this->loader->add_action('woocommerce_payment_complete_order_status_processing', $plugin_public, 'woocommerce_payment_complete', 10, 1);
		$this->loader->add_action('woocommerce_payment_complete_order_status_completed', $plugin_public, 'woocommerce_payment_complete', 10, 1);

		$this->loader->add_filter('woocommerce_defer_transactional_emails', $plugin_public, 'woocommerce_defer_transactional_emails', 10, 1);

		// Single Product
		$this->loader->add_action( 'woocommerce_product_meta_end', $plugin_public, 'woocommerce_product_meta_end', 50 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Tmsm_Woocommerce_Vouchers_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
