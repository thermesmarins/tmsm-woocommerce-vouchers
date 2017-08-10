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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-woocommerce-vouchers-public.js', array( 'jquery' ), $this->version, true );

	}

	/**
	 * Displays recipient form to single product
	 *
	 * @since    1.0.0
	 *
	 * @return void
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




			$settings_physical = get_option('tmsm_woocommerce_vouchers_physical') == 'yes';
			$settings_virtual = get_option('tmsm_woocommerce_vouchers_virtual') == 'yes';
			$settings_recipientoptionnal = get_option('tmsm_woocommerce_vouchers_recipientoptionnal') == 'yes';
			/*
			echo ' - $settings_physical:'.$settings_physical;
			echo ' - $settings_virtual:'.$settings_virtual;
			*/

			if($is_virtual && !$settings_virtual) $is_voucher = false;
			if(!$is_virtual && !$settings_physical) $is_voucher = false;

/*
			echo ' - $product_id:'.$product_id;
			echo ' - $variation_id:'.$variation_id;
			echo ' - $is_voucher:'.$is_voucher;
*/
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
				echo '<div class="vouchers-fields">';


				// recipientoptionnal
				if($settings_recipientoptionnal):
					echo '<tr class="vouchers-recipientoptionnal-trigger">';
					echo '<td class="label" colspan="2">';
					echo '<a href="#"><span class="glyphicon glyphicon-gift"></span> '.__( 'Set a voucher recipient', 'tmsm-woocommerce-vouchers' ).'</a>';
					echo '</td>';
					echo '</tr>';
				endif;

				// title
				if($settings_recipienttitle):
					woocommerce_form_field('_recipienttitle['.$variation_id.']', [
						'type' => 'select',
						'label' => __( 'Recipient title:', 'tmsm-woocommerce-vouchers' ),
						'description' => '',
						'required' => $settings_recipienttitlerequired,
						'autocomplete' => 'given-name',
						'id' => '_recipienttitle['.$variation_id.']',
						//'placeholder' => __( 'Pick a title', 'tmsm-woocommerce-vouchers' ),
						'options' => [
							'' => '',
							1 => __( 'Miss', 'tmsm-woocommerce-vouchers' ),
							2 => __( 'Mr', 'tmsm-woocommerce-vouchers' ),
						],
						'class' => [
							'form-row-wide',
							'title-field',
							'formfield-text',
							'form-group',
							($settings_recipienttitlerequired && isset($_POST['_recipienttitle'][$variation_id]) && empty($submit_recipienttitle)?'has-error':''),
						],
					], $submit_recipienttitle);

				endif;

				// firstname
				if($settings_recipientfirstname):
					woocommerce_form_field('_recipientfirstname['.$variation_id.']', [
						'type' => 'text',
						'label' => __( 'Recipient first name:', 'tmsm-woocommerce-vouchers' ),
						'description' => '',
						'required' => $settings_recipientfirstnamerequired,
						'autocomplete' => 'given-name',
						'id' => '_recipientfirstname['.$variation_id.']',
						'class' => [
							'form-row-wide',
							'firstname-field',
							'formfield-text',
							'form-group',
							($settings_recipientfirstnamerequired && isset($_POST['_recipientfirstname'][$variation_id]) && empty($submit_recipientfirstname)?'has-error':''),
						],
					], $submit_recipientfirstname);
				endif;

				// lastname
				if($settings_recipientlastname):
					woocommerce_form_field('_recipientlastname['.$variation_id.']', [
						'type' => 'text',
						'label' => __( 'Recipient last name:', 'tmsm-woocommerce-vouchers' ),
						'description' => '',
						'required' => $settings_recipientlastnamerequired,
						'autocomplete' => 'family-name',
						'id' => '_recipientlastname['.$variation_id.']',
						'class' => [
							'form-row-wide',
							'lastname-field',
							'formfield-text',
							'form-group',
							($settings_recipientlastnamerequired && isset($_POST['_recipientlastname'][$variation_id]) && empty($submit_recipientlastname)?'has-error':''),
						],
					], $submit_recipientlastname);
				endif;

				// address
				if($settings_recipientaddress):
					woocommerce_form_field('_recipientaddress['.$variation_id.']', [
						'type' => 'text',
						'label' => __( 'Recipient address:', 'tmsm-woocommerce-vouchers' ),
						'description' => '',
						'required' => $settings_recipientaddressrequired,
						'autocomplete' => 'address-line1',
						'id' => '_recipientaddress['.$variation_id.']',
						'class' => [
							'form-row-wide',
							'address-field',
							'formfield-text',
							'form-group',
							($settings_recipientaddressrequired && isset($_POST['_recipientaddress'][$variation_id]) && empty($submit_recipientaddress)?'has-error':''),
						],
					], $submit_recipientaddress);
				endif;

				// zipcode
				if($settings_recipientzipcode):
					woocommerce_form_field('_recipientzipcode['.$variation_id.']', [
						'type' => 'text',
						'label' => __( 'Recipient zipcode:', 'tmsm-woocommerce-vouchers' ),
						'description' => '',
						'required' => $settings_recipientzipcoderequired,
						'autocomplete' => 'postal-code',
						'id' => '_recipientzipcode['.$variation_id.']',
						'validate' => ['postcode'],
						'class' => [
							'form-row-wide',
							'address-field',
							'formfield-text',
							'form-group',
							($settings_recipientzipcoderequired && isset($_POST['_recipientzipcode'][$variation_id]) && empty($submit_recipientzipcode)?'has-error':''),
						],
					], $submit_recipientzipcode);
				endif;

				// city
				if($settings_recipientcity):
					woocommerce_form_field('_recipientcity['.$variation_id.']', [
						'type' => 'text',
						'label' => __( 'Recipient city:', 'tmsm-woocommerce-vouchers' ),
						'description' => '',
						'required' => $settings_recipientcityrequired,
						'autocomplete' => 'address-level2',
						'id' => '_recipientcity['.$variation_id.']',
						'class' => [
							'form-row-wide',
							'address-field',
							'formfield-text',
							'form-group',
							($settings_recipientcityrequired && isset($_POST['_recipientcity'][$variation_id]) && empty($submit_recipientcity)?'has-error':''),
						],
					], $submit_recipientcity);
				endif;

				// country
				if($settings_recipientcountry):
					woocommerce_form_field('_recipientcountry['.$variation_id.']', [
						'type' => 'country',
						'label' => __( 'Recipient country:', 'tmsm-woocommerce-vouchers' ),
						'description' => '',
						'required' => $settings_recipientcountryrequired,
						'autocomplete' => 'country',
						'id' => '_recipientcountry['.$variation_id.']',
					    'class' => [
					    	'form-group single-country'
					    ],
					], $submit_recipientcountry);

				endif;

				echo '</div>';
				echo '</div>';

			}
		}

		// restore product
		$product	= $reset_product;

	}

	/**
	 * Validates recipient data before adding to cart
	 *
	 * @since 1.0.0
	 *
	 * @param bool    $valid
	 * @param integer $product_id
	 * @param integer $quantity
	 * @param integer $variation_id
	 * @param array   $variations
	 * @param array   $cart_item_data
	 *
	 * @return bool $valid
	 */
	public function woocommerce_add_to_cart_validation($valid, $product_id, $quantity, $variation_id = '', $variations = array(), $cart_item_data = array()){

		$variation_id = $variation_id ? $variation_id : $product_id;
		$product = wc_get_product($variation_id);

		$is_voucher = get_post_meta( $variation_id, '_voucher', true ) == 'yes';

		if($is_voucher){

			$settings_recipientoptionnal = get_option('tmsm_woocommerce_vouchers_recipientoptionnal') == 'yes';

			// title
			$settings_recipienttitle = get_option('tmsm_woocommerce_vouchers_recipienttitle') == 'yes';
			$settings_recipienttitlerequired = get_option('tmsm_woocommerce_vouchers_recipienttitlerequired') == 'yes';
			$submit_recipienttitle = isset( $_POST['_recipienttitle'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipienttitle'][$variation_id] ) : '';
			if (!$settings_recipientoptionnal && $settings_recipienttitlerequired && empty($submit_recipienttitle) ) {
				wc_add_notice('<p class="vouchers-fields-error">' . __('Recipient title', 'tmsm-woocommerce-vouchers') .' ' . __('is required.', 'tmsm-woocommerce-vouchers') . '</p>', 'error');
				$valid = false;
			}

			// firstname
			$settings_recipientfirstname = get_option('tmsm_woocommerce_vouchers_recipientfirstname') == 'yes';
			$settings_recipientfirstnamerequired = get_option('tmsm_woocommerce_vouchers_recipientfirstnamerequired') == 'yes';
			$submit_recipientfirstname = isset( $_POST['_recipientfirstname'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientfirstname'][$variation_id] ) : '';
			if (!$settings_recipientoptionnal && $settings_recipientfirstnamerequired && empty($submit_recipientfirstname) ) {
				wc_add_notice('<p class="vouchers-fields-error">' . __('Recipient first name', 'tmsm-woocommerce-vouchers') .' ' . __('is required.', 'tmsm-woocommerce-vouchers') . '</p>', 'error');
				$valid = false;
			}

			// lastname
			$settings_recipientlastname = get_option('tmsm_woocommerce_vouchers_recipientlastname') == 'yes';
			$settings_recipientlastnamerequired = get_option('tmsm_woocommerce_vouchers_recipientlastnamerequired') == 'yes';
			$submit_recipientlastname = isset( $_POST['_recipientlastname'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientlastname'][$variation_id] ) : '';
			if (!$settings_recipientoptionnal && $settings_recipientlastnamerequired && empty($submit_recipientlastname) ) {
				wc_add_notice('<p class="vouchers-fields-error">' . __('Recipient last name', 'tmsm-woocommerce-vouchers') .' ' . __('is required.', 'tmsm-woocommerce-vouchers') . '</p>', 'error');
				$valid = false;
			}
			
			// address
			$settings_recipientaddress = get_option('tmsm_woocommerce_vouchers_recipientaddress') == 'yes';
			$settings_recipientaddressrequired = get_option('tmsm_woocommerce_vouchers_recipientaddressrequired') == 'yes';
			$submit_recipientaddress = isset( $_POST['_recipientaddress'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientaddress'][$variation_id] ) : '';
			if (!$settings_recipientoptionnal && $settings_recipientaddressrequired && empty($submit_recipientaddress) ) {
				wc_add_notice('<p class="vouchers-fields-error">' . __('Recipient address', 'tmsm-woocommerce-vouchers') .' ' . __('is required.', 'tmsm-woocommerce-vouchers') . '</p>', 'error');
				$valid = false;
			}

			// zipcode
			$settings_recipientzipcode = get_option('tmsm_woocommerce_vouchers_recipientzipcode') == 'yes';
			$settings_recipientzipcoderequired = get_option('tmsm_woocommerce_vouchers_recipientzipcoderequired') == 'yes';
			$submit_recipientzipcode = isset( $_POST['_recipientzipcode'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientzipcode'][$variation_id] ) : '';
			if (!$settings_recipientoptionnal && $settings_recipientzipcoderequired && empty($submit_recipientzipcode) ) {
				wc_add_notice('<p class="vouchers-fields-error">' . __('Recipient zipcode', 'tmsm-woocommerce-vouchers') .' ' . __('is required.', 'tmsm-woocommerce-vouchers') . '</p>', 'error');
				$valid = false;
			}

			// city
			$settings_recipientcity = get_option('tmsm_woocommerce_vouchers_recipientcity') == 'yes';
			$settings_recipientcityrequired = get_option('tmsm_woocommerce_vouchers_recipientcityrequired') == 'yes';
			$submit_recipientcity = isset( $_POST['_recipientcity'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientcity'][$variation_id] ) : '';
			if (!$settings_recipientoptionnal && $settings_recipientcityrequired && empty($submit_recipientcity) ) {
				wc_add_notice('<p class="vouchers-fields-error">' . __('Recipient city', 'tmsm-woocommerce-vouchers') .' ' . __('is required.', 'tmsm-woocommerce-vouchers') . '</p>', 'error');
				$valid = false;
			}

			// country
			$settings_recipientcountry = get_option('tmsm_woocommerce_vouchers_recipientcountry') == 'yes';
			$settings_recipientcountryrequired = get_option('tmsm_woocommerce_vouchers_recipientcountryrequired') == 'yes';
			$submit_recipientcountry = isset( $_POST['_recipientcountry'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientcountry'][$variation_id] ) : '';
			if (!$settings_recipientoptionnal && $settings_recipientcountryrequired && empty($submit_recipientcountry) ) {
				wc_add_notice('<p class="vouchers-fields-error">' . __('Recipient country', 'tmsm-woocommerce-vouchers') .' ' . __('is required.', 'tmsm-woocommerce-vouchers') . '</p>', 'error');
				$valid = false;
			}



		}
		return $valid;

	}

	/**
	 * Add recipient data to cart item meta when product is added to cart
	 *
	 * @since 1.0.0
	 *
	 * @param array $cart_item_data
	 * @param integer $product_id
	 * @param integer $variation_id
	 *
	 * @return array $cart_item_data
	 */
	public function woocommerce_add_cart_item_data($cart_item_data, $product_id, $variation_id){

		$variation_id = $variation_id ? $variation_id : $product_id;

		// title
		$submit_recipienttitle = isset( $_POST['_recipienttitle'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipienttitle'][$variation_id] ) : '';
		$cart_item_data['_recipienttitle'] = $submit_recipienttitle;

		// firstname
		$submit_recipientfirstname = isset( $_POST['_recipientfirstname'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientfirstname'][$variation_id] ) : '';
		$cart_item_data['_recipientfirstname'] = $submit_recipientfirstname;

		// lastname
		$submit_recipientlastname = isset( $_POST['_recipientlastname'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientlastname'][$variation_id] ) : '';
		$cart_item_data['_recipientlastname'] = $submit_recipientlastname;

		// address
		$submit_recipientaddress = isset( $_POST['_recipientaddress'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientaddress'][$variation_id] ) : '';
		$cart_item_data['_recipientaddress'] = $submit_recipientaddress;
		
		// zipcode
		$submit_recipientzipcode = isset( $_POST['_recipientzipcode'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientzipcode'][$variation_id] ) : '';
		$cart_item_data['_recipientzipcode'] = $submit_recipientzipcode;
		
		// city
		$submit_recipientcity = isset( $_POST['_recipientcity'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientcity'][$variation_id] ) : '';
		$cart_item_data['_recipientcity'] = $submit_recipientcity;
		
		// country
		$submit_recipientcountry = isset( $_POST['_recipientcountry'][$variation_id] ) ? wp_filter_nohtml_kses( $_POST['_recipientcountry'][$variation_id] ) : '';
		$cart_item_data['_recipientcountry'] = $submit_recipientcountry;

		return $cart_item_data;
	}

	/**
	 * Displays recipient data on cart page, checkout page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data
	 * @param array $item
	 *
	 * @return array $data
	 */
	public function woocommerce_get_item_data($data, $item) {

		$product_id = isset($item['product_id']) ? $item['product_id'] : '';

		switch($item['_recipienttitle'])
		{
			case 1: $title = __('Miss', 'tmsm-woocommerce-vouchers'). ' '; break;
			case 2: $title = __('Mr', 'tmsm-woocommerce-vouchers'). ' '; break;
			default: $title =''; break;

		}
		$recipient = array(
			'first_name'  => $title.$item['_recipientfirstname'],
			'last_name'   => $item['_recipientlastname'],
			'address_1'   => $item['_recipientaddress'],
			//'address_2'   => '',
			'city'        => $item['_recipientcity'],
			'postcode'    => $item['_recipientzipcode'],
			//'state'    => '',
			'country'     => $item['_recipientcountry'],
		);

		$formatted_recipient = WC()->countries->get_formatted_address( $recipient );
		$data[] = array(
			'name' => __('Recipient', 'tmsm-woocommerce-vouchers'),
			'display' => $formatted_recipient,
			'hidden' => false,
			'value' => ''
		);




		return $data;
	}


	/**
	 * Update order item's meta with recipient data
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order_Item_Product $item
	 * @param string                $cart_item_key
	 * @param array                 $values
	 * @param WC_Order              $order
	 */
	public function woocommerce_checkout_create_order_line_item($item, $cart_item_key, $values, $order){

		$variation_id = isset($values['variation_id']) && !empty($values['variation_id']) ? $values['variation_id'] : $values['product_id'];

		$product = $item->get_product();

		if($product){
			$variation_id = $product->get_id();
		}
		$is_virtual = get_post_meta( $variation_id, '_virtual', true ) == 'yes';
		$is_voucher = get_post_meta( $variation_id, '_voucher', true ) == 'yes';

		if($is_virtual){
			$item->add_meta_data( '_virtual', 'yes', true );
		}
		else{
			$item->add_meta_data( '_virtual', 'no', true );
		}

		if($is_voucher){
			$item->add_meta_data( '_voucher', 'yes', true );
		}

		if (!empty($values['_recipienttitle'])) {
			$item->add_meta_data( '_recipienttitle', $values['_recipienttitle'], true );
		}

		if (!empty($values['_recipientfirstname'])) {
			$item->add_meta_data( '_recipientfirstname', $values['_recipientfirstname'], true );
		}

		if (!empty($values['_recipientlastname'])) {
			$item->add_meta_data( '_recipientlastname', $values['_recipientlastname'], true );
		}

		if (!empty($values['_recipientaddress'])) {
			$item->add_meta_data( '_recipientaddress', $values['_recipientaddress'], true );
		}

		if (!empty($values['_recipientzipcode'])) {
			$item->add_meta_data( '_recipientzipcode', $values['_recipientzipcode'], true );
		}

		if (!empty($values['_recipientcity'])) {
			$item->add_meta_data( '_recipientcity', $values['_recipientcity'], true );
		}

		if (!empty($values['_recipientcountry'])) {
			$item->add_meta_data( '_recipientcountry', $values['_recipientcountry'], true );
		}

	}


	/**
	 * Displays hidden delivery date for order item in order view (frontend)
	 * /my-account/view-order/$order_id/
	 * /checkout/order-received/$order_id/
	 *
	 * @since 1.0.0
	 *
	 * @param  string        $html
	 * @param  WC_Order_Item $item
	 * @param  array         $args
	 *
	 * @return string
	 */
	public function woocommerce_display_item_meta($html, $item, $args)
	{

		$strings = [];

		// title
		$recipienttitle = $item->get_meta( '_recipienttitle' );
		if ( isset( $recipienttitle ) && ! empty( $recipienttitle ) ) {
			$strings[] = '<strong class="wc-item-meta-label">' . __('Recipient title:', 'tmsm-woocommerce-vouchers') . '</strong> ' . ($recipienttitle == 1?__('Miss', 'tmsm-woocommerce-vouchers'):__('Mr', 'tmsm-woocommerce-vouchers'));
		}

		// firstname
		$recipientfirstname = $item->get_meta( '_recipientfirstname' );
		if ( isset( $recipientfirstname ) && ! empty( $recipientfirstname ) ) {
			$strings[] = '<strong class="wc-item-meta-label">' . __('Recipient first name:', 'tmsm-woocommerce-vouchers') . '</strong> ' . wp_kses_post($recipientfirstname);
		}

		// lastname
		$recipientlastname = $item->get_meta( '_recipientlastname' );
		if ( isset( $recipientlastname ) && ! empty( $recipientlastname ) ) {
			$strings[] = '<strong class="wc-item-meta-label">' . __('Recipient last name:', 'tmsm-woocommerce-vouchers') . '</strong> ' . wp_kses_post($recipientlastname);
		}

		// address
		$recipientaddress = $item->get_meta( '_recipientaddress' );
		if ( isset( $recipientaddress ) && ! empty( $recipientaddress ) ) {
			$strings[] = '<strong class="wc-item-meta-label">' . __('Recipient address:', 'tmsm-woocommerce-vouchers') . '</strong> ' . wp_kses_post($recipientaddress);
		}

		// zipcode
		$recipientzipcode = $item->get_meta( '_recipientzipcode' );
		if ( isset( $recipientzipcode ) && ! empty( $recipientzipcode ) ) {
			$strings[] = '<strong class="wc-item-meta-label">' . __('Recipient zipcode:', 'tmsm-woocommerce-vouchers') . '</strong> ' . wp_kses_post($recipientzipcode);
		}

		// city
		$recipientcity = $item->get_meta( '_recipientcity' );
		if ( isset( $recipientcity ) && ! empty( $recipientcity ) ) {
			$strings[] = '<strong class="wc-item-meta-label">' . __('Recipient city:', 'tmsm-woocommerce-vouchers') . '</strong> ' . wp_kses_post($recipientcity);
		}
		
		// country
		$recipientcountry = $item->get_meta( '_recipientcountry' );
		if ( isset( $recipientcountry ) && ! empty( $recipientcountry ) ) {
			$strings[] = '<strong class="wc-item-meta-label">' . __('Recipient country:', 'tmsm-woocommerce-vouchers') . '</strong> ' . wp_kses_post($recipientcountry);
		}

		if ($strings != []){
			$html = $args['before'] . implode( $args['separator'], $strings ) . $args['after'];
		}
		return $html;
	}


}
