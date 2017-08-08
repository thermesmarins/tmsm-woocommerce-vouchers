<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/thermesmarins/
 * @since      1.0.0
 *
 * @package    Tmsm_Woocommerce_Vouchers
 * @subpackage Tmsm_Woocommerce_Vouchers/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tmsm_Woocommerce_Vouchers
 * @subpackage Tmsm_Woocommerce_Vouchers/public
 * @author     Nicolas MOLLET <nmollet@thalassotherapie.com>
 */
class Tmsm_Woocommerce_Vouchers_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-woocommerce-vouchers-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-woocommerce-vouchers-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * WooCommerce Single Product: before add to cart button
	 *
	 * @since    1.0.0
	 */
	public function woocommerce_before_add_to_cart_button() {

		global $product;

		// store product in reset variable
		$reset_product	= $product;

		//Initilize products
		$products = array();

		if ( $product->is_type( 'variable' ) ) { //for variable product
			foreach ( $product->get_children() as $variation_product_id ) {
				$products[] = wc_get_product( $variation_product_id );
			}
		} else {
			$products[] = $product;
		}

		foreach ( $products as $product ) {//For all products
			
			// Get product ID
			$product_id = $variation_id = $product->get_id();
			$is_voucher = false;
			if( $product->is_type( 'variation' ) ) {
				// Get product ID
				//$product_id 	= $woo_vou_model->woo_vou_get_item_productid_from_product($product);
				if ( $product->is_type( 'variable' ) || $product->is_type( 'variation' ) ) {
					$product_id = $product->get_parent_id();

				} else {
					$product_id = $product->get_id();
				}
				// Get variation ID
				$variation_id = $product->get_id();
			}

			$is_voucher = get_post_meta( $variation_id, '_voucher', true ) == 'yes';
			$is_virtual = get_post_meta( $variation_id, '_virtual', true ) == 'yes';
			$is_downloadable = get_post_meta( $variation_id, '_downloadable', true ) == 'yes';

			/*
			echo ' - $product_id:'.$product_id;
			echo ' - $variation_id:'.$variation_id;
			echo ' - $is_voucher:'.$is_voucher;
			*/

			$settings_physical = get_option('tmsm_woocommerce_vouchers_physical') == 'yes';
			$settings_virtual = get_option('tmsm_woocommerce_vouchers_virtual') == 'yes';
			$settings_recipientoptionnal = get_option('tmsm_woocommerce_vouchers_recipientoptionnal') == 'yes';
			/*
			echo ' - $settings_physical:'.$settings_physical;
			echo ' - $settings_virtual:'.$settings_virtual;
			*/

			if($is_virtual && !$settings_virtual) $is_voucher = false;
			if(!$is_virtual && !$settings_physical) $is_voucher = false;

			if( $is_voucher ) { // if voucher is enable


				$settings_recipientfirstname = get_option('tmsm_woocommerce_vouchers_recipientfirstname') == 'yes';
				$settings_recipientfirstnamerequired = get_option('tmsm_woocommerce_vouchers_recipientfirstnamerequired') == 'yes';

				$settings_recipientlastname = get_option('tmsm_woocommerce_vouchers_recipientlastname') == 'yes';
				$settings_recipientlastnamerequired = get_option('tmsm_woocommerce_vouchers_recipientlastnamerequired') == 'yes';

				$settings_recipientbirthdate = get_option('tmsm_woocommerce_vouchers_recipientbirthdate') == 'yes';
				$settings_recipientbirthdaterequired = get_option('tmsm_woocommerce_vouchers_recipientbirthdaterequired') == 'yes';

				$settings_recipienttitle = get_option('tmsm_woocommerce_vouchers_recipienttitle') == 'yes';
				$settings_recipienttitlerequired = get_option('tmsm_woocommerce_vouchers_recipienttitlerequired') == 'yes';

				$settings_recipientaddress = get_option('tmsm_woocommerce_vouchers_recipientaddress') == 'yes';
				$settings_recipientaddressrequired = get_option('tmsm_woocommerce_vouchers_recipientaddressrequired') == 'yes';

				$settings_recipientzipcode = get_option('tmsm_woocommerce_vouchers_recipientzipcode') == 'yes';
				$settings_recipientzipcoderequired = get_option('tmsm_woocommerce_vouchers_recipientzipcoderequired') == 'yes';

				$settings_recipientcity = get_option('tmsm_woocommerce_vouchers_recipientcity') == 'yes';
				$settings_recipientcityrequired = get_option('tmsm_woocommerce_vouchers_recipientcityrequired') == 'yes';

				$settings_recipientcountry = get_option('tmsm_woocommerce_vouchers_recipientcountry') == 'yes';
				$settings_recipientcountryrequired = get_option('tmsm_woocommerce_vouchers_recipientcountryrequired') == 'yes';

				$settings_recipientmobilephone = get_option('tmsm_woocommerce_vouchers_recipientmobilephone') == 'yes';
				$settings_recipientmobilephonerequired = get_option('tmsm_woocommerce_vouchers_recipientmobilephonerequired') == 'yes';

				$settings_recipientemail = get_option('tmsm_woocommerce_vouchers_recipientemail') == 'yes';
				$settings_recipientemailrequired = get_option('tmsm_woocommerce_vouchers_recipientemailrequired') == 'yes';

				$settings_recipientmessage = get_option('tmsm_woocommerce_vouchers_recipientmessage') == 'yes';
				$settings_recipientmessagerequired = get_option('tmsm_woocommerce_vouchers_recipientmessagerequired') == 'yes';

				$settings_recipientsenddate = get_option('tmsm_woocommerce_vouchers_recipientsenddate') == 'yes';
				$settings_recipientsenddaterequired = get_option('tmsm_woocommerce_vouchers_recipientsenddaterequired') == 'yes';





				$submit_recipientfirstname = isset( $_POST['_recipientfirstname'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientfirstname'][$variation_id] ) : '';

				$submit_recipientlastname = isset( $_POST['_recipientlastname'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientlastname'][$variation_id] ) : '';

				$submit_recipienttitle = isset( $_POST['_recipienttitle'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipienttitle'][$variation_id] ) : '';

				$submit_recipientbirthdate = isset( $_POST['_recipientbirthdate'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientbirthdate'][$variation_id] ) : '';

				$submit_recipientaddress = isset( $_POST['_recipientaddress'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientaddress'][$variation_id] ) : '';

				$submit_recipientzipcode = isset( $_POST['_recipientzipcode'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientzipcode'][$variation_id] ) : '';

				$submit_recipientcity = isset( $_POST['_recipientcity'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientcity'][$variation_id] ) : '';

				$submit_recipientcountry = isset( $_POST['_recipientcountry'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientcountry'][$variation_id] ) : '';

				$submit_recipientmobilephone = isset( $_POST['_recipientmobilephone'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientmobilephone'][$variation_id] ) : '';

				$submit_recipientemail = isset( $_POST['_recipientemail'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientemail'][$variation_id] ) : '';

				$submit_recipientmessage = isset( $_POST['_recipientmessage'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientmessage'][$variation_id] ) : '';

				$submit_recipientsenddate = isset( $_POST['_recipientsenddate'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientsenddate'][$variation_id] ) : '';


				echo '<div class="vouchers-fields-wrapper'.($product->is_type( 'variation' )?'-variation':'').'" id="vouchers-fields-wrapper-'.$variation_id.'" >';
				echo '<p class="h4 vouchers-fields-title">';
				echo __( 'Recipient of the voucher', 'tmsm-woocommerce-vouchers' );
				echo '</p>';
				echo '<table class="variations variations-recipient vouchers-fields" cellspacing="0">';
				echo '<tbody>';


				// recipientoptionnal
				if($settings_recipientoptionnal):
					echo '<tr class="vouchers-recipientoptionnal-trigger">';
					echo '<td class="label" colspan="2">';
					echo '<a href="#"><span class="glyphicon glyphicon-gift"></span> '.__( 'Set a voucher recipient', 'tmsm-woocommerce-vouchers' ).'</a>';
					echo '</td>';
					echo '</tr>';
				endif;

				// firstname
				echo '<tr class="'.($settings_recipientoptionnal?'vouchers-recipientoptionnal':'').'">';
				echo '<td class="label">';
				echo '<label class="control-label" for="recipient_name-'.$variation_id.'">';
				echo __( 'Recipient first name', 'tmsm-woocommerce-vouchers' );
				echo '</label>';
				echo '</td>';
				echo '<td class="value">';
				echo '<input type="text" class="input-text form-control" value="'.$submit_recipientfirstname.'" id="recipient_name-'.$variation_id.'" name="_recipientfirstname['.$variation_id.']">';
				echo '</td>';
				echo '</tr>';


				echo '</tbody>';
				echo '</table>';
				echo '</div>';

					//woo_vou_get_template( 'woo-vou-recipient-fields.php', $args );

			}
		}

		// restore product
		$product	= $reset_product;

	}

	/**
	 * This is used to ensure any required user input fields are supplied
	 *
	 * Handles to This is used to ensure any required user input fields are supplied
	 *
	 * @param        $valid
	 * @param        $product_id
	 * @param        $quantity
	 * @param string $variation_id
	 * @param array  $variations
	 * @param array  $cart_item_data
	 *
	 * @since 1.0.0
	 */
	/**

	 */
	public function woocommerce_add_to_cart_validation($valid, $product_id, $quantity, $variation_id = '', $variations = array(), $cart_item_data = array()){

		$variation_id = $variation_id ? $variation_id : $product_id;
		$product = wc_get_product($variation_id);

		$is_voucher = get_post_meta( $variation_id, '_voucher', true ) == 'yes';

		if($is_voucher){

			$settings_recipientoptionnal = get_option('tmsm_woocommerce_vouchers_recipientoptionnal') == 'yes';

			$settings_recipientfirstname = get_option('tmsm_woocommerce_vouchers_recipientfirstname') == 'yes';
			$settings_recipientfirstnamerequired = get_option('tmsm_woocommerce_vouchers_recipientfirstnamerequired') == 'yes';

			$submit_recipientfirstname = isset( $_POST['_recipientfirstname'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientfirstname'][$variation_id] ) : '';

			/*
			wc_add_notice('$submit_recipientfirstname:'.$submit_recipientfirstname);
			wc_add_notice('!$settings_recipientoptionnal:'.!$settings_recipientoptionnal);
			wc_add_notice('$settings_recipientfirstnamerequired:'.$settings_recipientfirstnamerequired);
			wc_add_notice('empty($submit_recipientfirstname):'.empty($submit_recipientfirstname));
			*/

			// validation
			if (!$settings_recipientoptionnal && $settings_recipientfirstnamerequired && empty($submit_recipientfirstname) ) {
				wc_add_notice('<p class="vouchers-fields-error">' . __('Recipient first name', 'tmsm-woocommerce-vouchers') .' ' . __('is required.', 'woovoucher') . '</p>', 'error');
				$valid = false;
			}

		}
		return $valid;

	}

	/**
	 * Add item data to cart
	 *
	 * @param $cart_item_data
	 * @param $product_id
	 * @param $variation_id
	 *
	 * @since 1.0.0
	 */
	public function woocommerce_add_cart_item_data($cart_item_data, $product_id, $variation_id){

		$variation_id = $variation_id ? $variation_id : $product_id;

		$submit_recipientfirstname = isset( $_POST['_recipientfirstname'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientfirstname'][$variation_id] ) : '';
		$cart_item_data['_recipientfirstname'] = $submit_recipientfirstname;

		return $cart_item_data;
	}

	/**
	 * Get to cart in item data to display in cart page
	 *
	 * @param $data
	 * @param $item
	 *
	 * @since 1.0.0
	 */
	public function woocommerce_get_item_data($data, $item) {

		$product_id = isset($item['product_id']) ? $item['product_id'] : '';


		if(!empty($item['_recipientfirstname'])){
			$data[] = array(
				'name' => __('Recipient first name', 'tmsm-woocommerce-vouchers'),
				'display' => $item['_recipientfirstname'],
				'hidden' => false,
				'value' => ''
			);
		}

		return $data;
	}



}
