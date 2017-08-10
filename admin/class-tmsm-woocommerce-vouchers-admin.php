<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/thermesmarins/
 * @since      1.0.0
 *
 * @package    Tmsm_Woocommerce_Vouchers
 * @subpackage Tmsm_Woocommerce_Vouchers/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tmsm_Woocommerce_Vouchers
 * @subpackage Tmsm_Woocommerce_Vouchers/admin
 * @author     Nicolas MOLLET <nmollet@thalassotherapie.com>
 */
class Tmsm_Woocommerce_Vouchers_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tmsm_Woocommerce_Vouchers_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tmsm_Woocommerce_Vouchers_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-woocommerce-vouchers-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tmsm_Woocommerce_Vouchers_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tmsm_Woocommerce_Vouchers_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-woocommerce-vouchers-admin.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * Add a custom product tab "voucher"
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	function woocommerce_product_data_tabs_voucher( $tabs) {
		$tabs['voucher'] = array(
			'label'		=> __( 'Voucher', 'tmsm-woocommerce-vouchers' ),
			'target'	=> 'voucher_options',
			'class'		=> array( 'show_if_simple', 'show_if_variable'  ),
		);
		return $tabs;
	}

	/**
	 * Tab content of tab "voucher"
	 */
	function woocommerce_product_data_panels_voucher() {
		global $post;

		// Note the 'id' attribute needs to match the 'target' parameter set above
		?><div id='voucher_options' class='panel woocommerce_options_panel'><?php
		?><div class='options_group'>


		<p>
			<?php echo __( 'No options at the moment', 'tmsm-woocommerce-vouchers' ); ?>
		</p>

		<?php
		/*
		woocommerce_wp_checkbox( array(
			'id' 		=> '_allow_personal_message',
			'label' 	=> __( 'Allow the customer to add a personal message', 'woocommerce' ),
		) );
		woocommerce_wp_text_input( array(
			'id'				=> '_valid_for_days',
			'label'				=> __( 'Gift card validity (in days)', 'woocommerce' ),
			'desc_tip'			=> 'true',
			'description'		=> __( 'Enter the number of days the gift card is valid for.', 'woocommerce' ),
			'type' 				=> 'number',
			'custom_attributes'	=> array(
				'min'	=> '1',
				'step'	=> '1',
			),
		) );
		*/
		?></div>

		</div><?php
	}


	/**
	 * Save options for tab "voucher"
	 *
	 * @param $post_id
	 */
	function woocommerce_process_product_save_voucher_options( $post_id ) {
		$is_voucher = isset( $_POST['_voucher'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_voucher', $is_voucher );
		/*
		$allow_personal_message = isset( $_POST['_allow_personal_message'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_allow_personal_message', $allow_personal_message );

		if ( isset( $_POST['_valid_for_days'] ) ) :
			update_post_meta( $post_id, '_valid_for_days', absint( $_POST['_valid_for_days'] ) );
		endif;
		*/
	}

	/**
	 * Product type "voucher"
	 *
	 * @param $product_type_options
	 *
	 * @return mixed
	 */
	function woocommerce_product_type_options_voucher( $product_type_options ) {
		$product_type_options['voucher'] = array(
			'id'            => '_voucher',
			'wrapper_class' => 'show_if_simple',
			'label'         => __( 'Voucher', 'tmsm-woocommerce-vouchers' ),
			'description'   => __( 'Vouchers', 'tmsm-woocommerce-vouchers' ),
			'default'       => 'no'
		);
		return $product_type_options;
	}


	/**
	 * Product variation type "voucher"
	 *
	 * @param $loop
	 * @param $variation_data
	 * @param $variation
	 */
	public function woocommerce_variation_options_voucher( $loop, $variation_data, $variation ) {
		$is_voucher = ( isset( $variation_data['_voucher'] ) && 'yes' == $variation_data['_voucher'][0] );
		echo '<label class="notips"><input type="checkbox" class="checkbox variable_is_voucher" name="variable_is_voucher[' . $loop . ']"' . checked( true, $is_voucher, false ) . '> '.__( 'Voucher', 'tmsm-woocommerce-vouchers' ).'</label>' . PHP_EOL;
	}

	/**
	 * Save product variation type "voucher"
	 *
	 * @param $variation_id
	 * @param $i
	 */
	public function woocommerce_save_product_variation_voucher( $post_id, $i ) {
		$is_voucher = isset( $_POST['variable_is_voucher'][ $i  ] ) ? 'yes' : 'no';
		update_post_meta( $post_id , '_voucher', $is_voucher );
	}


	/**
	 * Settings section
	 *
	 * @param $sections
	 *
	 * @return mixed
	 */
	function woocommerce_settings_tabs_array_vouchers( $sections ) {

		$sections['vouchers'] = __( 'Vouchers', 'tmsm-woocommerce-vouchers' );
		return $sections;

	}

	/**
	 * Include settings class
	 *
	 * @param $settings
	 *
	 * @return array
	 */
	function woocommerce_get_settings_pages_vouchers($settings) {
		//$settings[] = new WC_Settings_MyPlugin();
		//$settings[] = new WC_Settings_Rest_API();
		$settings[] = include( plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-tmsm-woocommerce-vouchers-settings.php' );
		return $settings; // Return
	}

	/**
	 * Hide order item meta from Order
	 *
	 * @param array $item_array
	 *
	 * @return array $item_array
	 */
	public function woocommerce_hidden_order_itemmeta($item_array = []){
		$item_meta[] = '_recipientfirstname';
		$item_meta[] = '_recipientlastname';
		$item_meta[] = '_recipienttitle';
		$item_meta[] = '_recipientbirthdate';
		$item_meta[] = '_recipientaddress';
		$item_meta[] = '_recipientzipcode';
		$item_meta[] = '_recipientcity';
		$item_meta[] = '_recipientcountry';
		$item_meta[] = '_recipientmobilephone';
		$item_meta[] = '_recipientemail';
		$item_meta[] = '_recipientmessage';
		$item_meta[] = '_recipientsenddate';

		return $item_meta;
	}

	/**
	 * Displays recipient item meta on order page
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @param mixed         $formatted_meta
	 * @param WC_Order_Item $order_item
	 *
	 * @return mixed
	 */
	public function woocommerce_order_item_get_formatted_meta_data( $formatted_meta, WC_Order_Item $order_item ) {
		if ( empty( $formatted_meta ) ) {
			return $formatted_meta;
		}

		foreach ( $formatted_meta as $meta ) {

			// title
			if($meta->key == '_recipienttitle' && !empty($meta->value)){
				$meta->display_key = __('Recipient title', 'tmsm-woocommerce-vouchers');
				$meta->display_value = ($meta->value == 1 ?__('Miss', 'tmsm-woocommerce-vouchers'):__('Mr', 'tmsm-woocommerce-vouchers'));
			}

			// firstname
			if($meta->key == '_recipientfirstname' && !empty($meta->value)){
				$meta->display_key = __('Recipient first name', 'tmsm-woocommerce-vouchers');
			}

		}

		return $formatted_meta;
	}

}
