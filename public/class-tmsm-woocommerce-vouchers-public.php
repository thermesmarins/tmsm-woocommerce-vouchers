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
 * @author     Nicolas Mollet <nmollet@thalassotherapie.com>
 */
class Tmsm_Woocommerce_Vouchers_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {


		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-woocommerce-vouchers-public.css', array(), $this->version,
			'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-woocommerce-vouchers-public.js', array( 'jquery', 'jquery-mask' ),
			$this->version, true );

		wp_enqueue_script( 'jquery-mask', plugin_dir_url( __FILE__ ) . 'js/jquery.mask.min.js', array( 'jquery' ), null, true );

		// Localize the script with new data
		$translation_array = array(
			'birthdateformat' => _x( 'mm/dd/yyyy', 'birth date format', 'tmsm-woocommerce-vouchers' ),
		);
		wp_localize_script( $this->plugin_name, 'tmsm_woocommerce_vouchers_i18n', $translation_array );

	}


	/**
	 * Enable Defer transactional emails
	 *
	 * @param bool $enable_defer
	 *
	 * @return bool $enable_defer
	 */
	public function woocommerce_defer_transactional_emails($enable_defer){
		$enable_defer = true;
		return $enable_defer;
	}

	/**
	 * Handles order modification after payment
	 *
	 * @param $order_id
	 *
	 * @return void
	 */
	public function woocommerce_payment_complete($order_id){

		$order = wc_get_order($order_id);

		$voucher_processed = get_post_meta( $order_id, '_voucherprocessed', true );

		if(!$order->is_paid() || $voucher_processed === 'yes'){
			return;
		}

		$customer_id = $order->get_customer_id();
		if ( $customer_id ) {
			$user = get_user_by( 'id', $customer_id );
			$username = trim( sprintf( _x( '%1$s %2$s', 'full name', 'tmsm-woocommerce-vouchers' ), $order->get_billing_first_name(), $order->get_billing_last_name() ) );
		}
		else{
			$username = __('Guest', 'tmsm-woocommerce-vouchers');
		}
		
		$order_items = $order->get_items();
		$order_date = $order->get_date_created();
		
		if (is_array($order_items)) {

			// Check cart details
			foreach ($order_items as $item_id => $item) {

				//get product id
				$product_id = $item['product_id'];

				// Taking variation id
				$variation_id = !empty($item['variation_id']) ? $item['variation_id'] : '';

				// If product is variable product take variation id else product id
				$data_id = (!empty($variation_id) ) ? $variation_id : $product_id;

				//Get voucher code from item meta "Now we store voucher codes in item meta fields"

				// No recipient, no voucher
				$has_recipient = !empty(wc_get_order_item_meta($item_id, '_recipientlastname'));
				if($has_recipient === false){
					continue;
				}

				// Check if codes already have been generated
				$codes_item_meta = wc_get_order_item_meta($item_id, '_vouchercode');

				if($this->tmsmvoucher_product_type_is_voucher_virtual($product_id, $variation_id) || $this->tmsmvoucher_product_type_is_voucher_physical($product_id, $variation_id)){

					$expirydate   = get_post_meta( $product_id, '_tmsm_woocommerce_vouchers_expirydate', true );
					// Expiry Date has to be in the future
					if(!empty($expirydate) && $expirydate < date('Y-m-d')){
						$expirydate = null;
					}

					if(empty($expirydate)){
						$expiremonths   = get_post_meta( $product_id, '_tmsm_woocommerce_vouchers_expiremonths', true );
						if(empty($expiremonths)){
							$expiremonths = get_option( 'tmsm_woocommerce_vouchers_expiremonths' );
						}

						if(!empty($expiremonths)){
							$expirydate = date('Y-m-d', strtotime($order_date . '+' . ( $expiremonths ) . ' months'));

						}
					}
					wc_add_order_item_meta($item_id, '_expirydate', $expirydate);
				}

				if (empty($codes_item_meta)) {// If voucher data are not empty so code get executed once only

					if($this->tmsmvoucher_product_type_is_voucher_virtual($product_id, $variation_id)){

						$codes = [];
						if (isset($item['qty'])) {
							for ($i = 1; $i <= $item['qty']; $i++) {
								$codes[] = $this->tmsmvoucher_generate_code($order_id, $product_id, $variation_id);
							}
						}

						if(!empty($codes)){
							wc_add_order_item_meta($item_id, '_vouchercode', implode(', ', $codes), true);

						}
					}

				}
			} // foreach $order_items

		}

		add_post_meta($order_id, '_voucherprocessed', 'yes', true);

		$order->save();
		return;
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
		$reset_product = $product;

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
			if ( $product->is_type( 'variation' ) ) {
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

			$enable_voucher = $this->tmsmvoucher_product_type_is_voucher_virtual($product_id, $variation_id) || $this->tmsmvoucher_product_type_is_voucher_physical($product_id, $variation_id);

			if ( $enable_voucher ) { // if voucher is enable

				$settings_recipientoptionnal         = get_option( 'tmsm_woocommerce_vouchers_recipientoptionnal' ) == 'yes';

				$settings_recipientfirstname         = get_option( 'tmsm_woocommerce_vouchers_recipientfirstname' ) == 'yes';
				$settings_recipientfirstnamerequired = get_option( 'tmsm_woocommerce_vouchers_recipientfirstnamerequired' ) == 'yes';

				$settings_recipientlastname         = get_option( 'tmsm_woocommerce_vouchers_recipientlastname' ) == 'yes';
				$settings_recipientlastnamerequired = get_option( 'tmsm_woocommerce_vouchers_recipientlastnamerequired' ) == 'yes';

				$settings_recipientbirthdate         = get_option( 'tmsm_woocommerce_vouchers_recipientbirthdate' ) == 'yes';
				$settings_recipientbirthdaterequired = get_option( 'tmsm_woocommerce_vouchers_recipientbirthdaterequired' ) == 'yes';

				$settings_recipienttitle         = get_option( 'tmsm_woocommerce_vouchers_recipienttitle' ) == 'yes';
				$settings_recipienttitlerequired = get_option( 'tmsm_woocommerce_vouchers_recipienttitlerequired' ) == 'yes';

				$settings_recipientaddress         = get_option( 'tmsm_woocommerce_vouchers_recipientaddress' ) == 'yes';
				$settings_recipientaddressrequired = get_option( 'tmsm_woocommerce_vouchers_recipientaddressrequired' ) == 'yes';

				$settings_recipientzipcode         = get_option( 'tmsm_woocommerce_vouchers_recipientzipcode' ) == 'yes';
				$settings_recipientzipcoderequired = get_option( 'tmsm_woocommerce_vouchers_recipientzipcoderequired' ) == 'yes';

				$settings_recipientcity         = get_option( 'tmsm_woocommerce_vouchers_recipientcity' ) == 'yes';
				$settings_recipientcityrequired = get_option( 'tmsm_woocommerce_vouchers_recipientcityrequired' ) == 'yes';

				$settings_recipientcountry         = get_option( 'tmsm_woocommerce_vouchers_recipientcountry' ) == 'yes';
				$settings_recipientcountryrequired = get_option( 'tmsm_woocommerce_vouchers_recipientcountryrequired' ) == 'yes';

				$settings_recipientmobilephone         = get_option( 'tmsm_woocommerce_vouchers_recipientmobilephone' ) == 'yes';
				$settings_recipientmobilephonerequired = get_option( 'tmsm_woocommerce_vouchers_recipientmobilephonerequired' ) == 'yes';

				$settings_recipientemail         = get_option( 'tmsm_woocommerce_vouchers_recipientemail' ) == 'yes';
				$settings_recipientemailrequired = get_option( 'tmsm_woocommerce_vouchers_recipientemailrequired' ) == 'yes';

				$settings_recipientmessage         = get_option( 'tmsm_woocommerce_vouchers_recipientmessage' ) == 'yes';
				$settings_recipientmessagerequired = get_option( 'tmsm_woocommerce_vouchers_recipientmessagerequired' ) == 'yes';

				$settings_recipientsenddate         = get_option( 'tmsm_woocommerce_vouchers_recipientsenddate' ) == 'yes';
				$settings_recipientsenddaterequired = get_option( 'tmsm_woocommerce_vouchers_recipientsenddaterequired' ) == 'yes';


				$submit_recipientfirstname = isset( $_POST['_recipientfirstname'][ $variation_id ] )
					? sanitize_text_field( $_POST['_recipientfirstname'][ $variation_id ] ) : '';

				$submit_recipientlastname = isset( $_POST['_recipientlastname'][ $variation_id ] )
					? sanitize_text_field( $_POST['_recipientlastname'][ $variation_id ] ) : '';

				$submit_recipienttitle = isset( $_POST['_recipienttitle'][ $variation_id ] )
					? sanitize_text_field( $_POST['_recipienttitle'][ $variation_id ] ) : '';

				$submit_recipientbirthdate = isset( $_POST['_recipientbirthdate'][ $variation_id ] )
					? sanitize_text_field( $_POST['_recipientbirthdate'][ $variation_id ] ) : '';

				$submit_recipientaddress = isset( $_POST['_recipientaddress'][ $variation_id ] )
					? sanitize_text_field( $_POST['_recipientaddress'][ $variation_id ] ) : '';

				$submit_recipientzipcode = isset( $_POST['_recipientzipcode'][ $variation_id ] )
					? sanitize_text_field( $_POST['_recipientzipcode'][ $variation_id ] ) : '';

				$submit_recipientcity = isset( $_POST['_recipientcity'][ $variation_id ] )
					? sanitize_text_field( $_POST['_recipientcity'][ $variation_id ] ) : '';

				$submit_recipientcountry = isset( $_POST['_recipientcountry'][ $variation_id ] )
					? sanitize_text_field( $_POST['_recipientcountry'][ $variation_id ] ) : '';

				$submit_recipientmobilephone = isset( $_POST['_recipientmobilephone'][ $variation_id ] )
					? sanitize_text_field( $_POST['_recipientmobilephone'][ $variation_id ] ) : '';

				$submit_recipientemail = isset( $_POST['_recipientemail'][ $variation_id ] )
					? sanitize_text_field( $_POST['_recipientemail'][ $variation_id ] ) : '';

				$submit_recipientmessage = isset( $_POST['_recipientmessage'][ $variation_id ] )
					? sanitize_text_field( $_POST['_recipientmessage'][ $variation_id ] ) : '';

				$submit_recipientsenddate = isset( $_POST['_recipientsenddate'][ $variation_id ] )
					? sanitize_text_field( $_POST['_recipientsenddate'][ $variation_id ] ) : '';


				echo '<div class="well vouchers-fields-wrapper' . ( $product->is_type( 'variation' ) ? '-variation' : '' )
				     . '" id="vouchers-fields-wrapper-' . $variation_id . '" >';
				echo '<h3 class=" first-heading vouchers-fields-title">';
				echo esc_html(get_option( 'tmsm_woocommerce_vouchers_recipientheading', __( 'Who to give this voucher to?', 'tmsm-woocommerce-vouchers' )));
				echo '</h3>';
				echo '<p>';
				echo __( 'Vouchers are nominative, <b>please fill the details of the recipient</b>.', 'tmsm-woocommerce-vouchers' );
				echo '</p>';

				echo '<div class="vouchers-fields">';

				$has_error = false;
				if($settings_recipienttitle){
					if(!$submit_recipienttitle){
						$has_error = true;
					}
				}
				if($settings_recipientfirstname){
					if(!$submit_recipientfirstname){
						$has_error = true;
					}
				}
				if($settings_recipientlastname){
					if(!$submit_recipientlastname){
						$has_error = true;
					}
				}
				if($settings_recipientbirthdaterequired){
					if(!$submit_recipientbirthdate){
						$has_error = true;
					}
				}
				if($settings_recipientaddress){
					if(!$submit_recipientaddress){
						$has_error = true;
					}
				}
				if($settings_recipientzipcode){
					if(!$submit_recipientzipcode){
						$has_error = true;
					}
				}
				if($settings_recipientcity){
					if(!$submit_recipientcity){
						$has_error = true;
					}
				}
				if($settings_recipientcountry){
					if(!$submit_recipientcountry){
						$has_error = true;
					}
				}
				if($settings_recipientmobilephone){
					if(!$submit_recipientmobilephone){
						$has_error = true;
					}
				}
				if($settings_recipientemail){
					if(!$submit_recipientemail){
						$has_error = true;
					}
				}

				// recipientoptionnal
				if ( $settings_recipientoptionnal ):
					echo '<tr class="vouchers-recipientoptionnal-trigger">';
					echo '<td class="label" colspan="2">';
					echo '<a href="#"><span class="glyphicon glyphicon-gift"></span> ' . __( 'Set a voucher recipient', 'tmsm-woocommerce-vouchers' )
					     . '</a>';
					echo '</td>';
					echo '</tr>';
				endif;

				// title
				if ( $settings_recipienttitle ):
					woocommerce_form_field( '_recipienttitle[' . $variation_id . ']', [
						'type'         => 'radio',
						'label'        => __( 'Recipient title:', 'tmsm-woocommerce-vouchers' ),
						'description'  => '',
						'required'     => $settings_recipienttitlerequired,
						'autocomplete' => 'honorific-prefix',
						'id'           => '_recipienttitle[' . $variation_id . ']',
						'options'      => [
							2  => __( 'Miss', 'tmsm-woocommerce-vouchers' ),
							1  => __( 'Mister', 'tmsm-woocommerce-vouchers' ),
						],
						'class'        => [
							'form-row-wide',
							'title-field',
							'formfield-text',
							'form-group',
							( $settings_recipienttitlerequired && $_POST && empty( $submit_recipienttitle ) ? 'has-error' : '' ),
						],
					], ($has_error ?$submit_recipienttitle :''));

				endif;

				// firstname
				if ( $settings_recipientfirstname ):
					woocommerce_form_field( '_recipientfirstname[' . $variation_id . ']', [
						'type'         => 'text',
						'label'        => __( 'Recipient first name:', 'tmsm-woocommerce-vouchers' ),
						'description'  => '',
						'required'     => $settings_recipientfirstnamerequired,
						'autocomplete' => 'given-name',
						'id'           => '_recipientfirstname[' . $variation_id . ']',
						'class'        => [
							'form-row-wide',
							'firstname-field',
							'formfield-text',
							'form-group',
							( $settings_recipientfirstnamerequired && isset( $_POST['_recipientfirstname'][ $variation_id ] )
							  && empty( $submit_recipientfirstname ) ? 'has-error' : '' ),
						],
					], ($has_error ? $submit_recipientfirstname :''));
				endif;

				// lastname
				if ( $settings_recipientlastname ):
					woocommerce_form_field( '_recipientlastname[' . $variation_id . ']', [
						'type'         => 'text',
						'label'        => __( 'Recipient last name:', 'tmsm-woocommerce-vouchers' ),
						'description'  => '',
						'required'     => $settings_recipientlastnamerequired,
						'autocomplete' => 'family-name',
						'id'           => '_recipientlastname[' . $variation_id . ']',
						'class'        => [
							'form-row-wide',
							'lastname-field',
							'formfield-text',
							'form-group',
							( $settings_recipientlastnamerequired && isset( $_POST['_recipientlastname'][ $variation_id ] )
							  && empty( $submit_recipientlastname ) ? 'has-error' : '' ),
						],
					], ($has_error ? $submit_recipientlastname:'') );
				endif;

				// birthdate
				if ( $settings_recipientbirthdate ):
					woocommerce_form_field( '_recipientbirthdate[' . $variation_id . ']', [
						'type'         => 'text',
						'label'        => __( 'Recipient birth date:', 'tmsm-woocommerce-vouchers' ),
						'description'  => '',
						'required'     => $settings_recipientbirthdaterequired,
						'autocomplete' => 'birthdate',
						'id'           => '_recipientbirthdate[' . $variation_id . ']',
						'class'        => [
							'tmsm-woocommerce-vouchers-maskedinput-birthdate',
							'form-row-wide',
							'birthdate-field',
							'formfield-text',
							'form-group',
							( $settings_recipientbirthdaterequired && isset( $_POST['_recipientbirthdate'][ $variation_id ] )
							  && empty( $submit_recipientbirthdate ) ? 'has-error' : '' ),
						],
					], ($has_error ? $submit_recipientbirthdate:'') );
				endif;

				// address
				if ( $settings_recipientaddress ):
					woocommerce_form_field( '_recipientaddress[' . $variation_id . ']', [
						'type'         => 'text',
						'label'        => __( 'Recipient address:', 'tmsm-woocommerce-vouchers' ),
						'description'  => '',
						'required'     => $settings_recipientaddressrequired,
						'autocomplete' => 'address-line1',
						'id'           => '_recipientaddress[' . $variation_id . ']',
						'class'        => [
							'form-row-wide',
							'address-field',
							'formfield-text',
							'form-group',
							( $settings_recipientaddressrequired && isset( $_POST['_recipientaddress'][ $variation_id ] )
							  && empty( $submit_recipientaddress ) ? 'has-error' : '' ),
						],
					], ($has_error ? $submit_recipientaddress :''));
				endif;

				// zipcode
				if ( $settings_recipientzipcode ):
					woocommerce_form_field( '_recipientzipcode[' . $variation_id . ']', [
						'type'         => 'text',
						'label'        => __( 'Recipient zipcode:', 'tmsm-woocommerce-vouchers' ),
						'description'  => '',
						'required'     => $settings_recipientzipcoderequired,
						'autocomplete' => 'postal-code',
						'id'           => '_recipientzipcode[' . $variation_id . ']',
						'validate'     => [ 'postcode' ],
						'class'        => [
							'form-row-wide',
							'address-field',
							'formfield-text',
							'form-group',
							( $settings_recipientzipcoderequired && isset( $_POST['_recipientzipcode'][ $variation_id ] )
							  && empty( $submit_recipientzipcode ) ? 'has-error' : '' ),
						],
					], ($has_error ? $submit_recipientzipcode :''));
				endif;

				// city
				if ( $settings_recipientcity ):
					woocommerce_form_field( '_recipientcity[' . $variation_id . ']', [
						'type'         => 'text',
						'label'        => __( 'Recipient city:', 'tmsm-woocommerce-vouchers' ),
						'description'  => '',
						'required'     => $settings_recipientcityrequired,
						'autocomplete' => 'address-level2',
						'id'           => '_recipientcity[' . $variation_id . ']',
						'class'        => [
							'form-row-wide',
							'address-field',
							'formfield-text',
							'form-group',
							( $settings_recipientcityrequired && isset( $_POST['_recipientcity'][ $variation_id ] ) && empty( $submit_recipientcity )
								? 'has-error' : '' ),
						],
					], ($has_error ? $submit_recipientcity :''));
				endif;

				// country
				if ( $settings_recipientcountry ):
					woocommerce_form_field( '_recipientcountry[' . $variation_id . ']', [
						'type'         => 'country',
						'label'        => __( 'Recipient country:', 'tmsm-woocommerce-vouchers' ),
						'description'  => '',
						'required'     => $settings_recipientcountryrequired,
						'autocomplete' => 'country-name',
						'id'           => '_recipientcountry[' . $variation_id . ']',
						'class'        => [
							'form-group single-country'
						],
					], ($has_error ? $submit_recipientcountry :''));

				endif;

				// mobilephone
				if ( $settings_recipientmobilephone ):
					woocommerce_form_field( '_recipientmobilephone[' . $variation_id . ']', [
						'type'         => 'tel',
						'label'        => __( 'Recipient mobilephone:', 'tmsm-woocommerce-vouchers' ),
						'description'  => '',
						'required'     => $settings_recipientmobilephonerequired,
						'autocomplete' => 'tel',
						'validate'     => [ 'phone' ],
						'id'           => '_recipientmobilephone[' . $variation_id . ']',
						'class'        => [
							'form-row-first',
							'mobilephone-field',
							'formfield-tel',
							'form-group',
							( $settings_recipientmobilephonerequired && isset( $_POST['_recipientmobilephone'][ $variation_id ] )
							  && empty( $submit_recipientmobilephone ) ? 'has-error' : '' ),
						],
					], ($has_error ? $submit_recipientmobilephone :''));
				endif;

				// email
				if ( $settings_recipientemail ):
					woocommerce_form_field( '_recipientemail[' . $variation_id . ']', [
						'type'         => 'email',
						'label'        => __( 'Recipient email:', 'tmsm-woocommerce-vouchers' ),
						'description'  => '',
						'required'     => $settings_recipientemailrequired,
						'autocomplete' => 'email username',
						'validate'     => [ 'email' ],
						'id'           => '_recipientemail[' . $variation_id . ']',
						'class'        => [
							'form-row-last',
							'formfield-email',
							'form-group',
							( $settings_recipientemailrequired && isset( $_POST['_recipientemail'][ $variation_id ] )
							  && empty( $submit_recipientemail ) ? 'has-error' : '' ),
						],
					], ($has_error ? $submit_recipientemail :'') );
				endif;


				echo '</div>';
				echo '</div>';

			}
		}

		// restore product
		$product = $reset_product;

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
	public function woocommerce_add_to_cart_validation(
		$valid, $product_id, $quantity, $variation_id = '', $variations = array(), $cart_item_data = array()
	) {

		$variation_id = $variation_id ? $variation_id : $product_id;
		$product      = wc_get_product( $variation_id );

		$enable_voucher = $this->tmsmvoucher_product_type_is_voucher_virtual($product_id, $variation_id) || $this->tmsmvoucher_product_type_is_voucher_physical($product_id, $variation_id);

		if ( $enable_voucher ) {

			$settings_recipientoptionnal = get_option( 'tmsm_woocommerce_vouchers_recipientoptionnal' ) == 'yes';

			// title
			$settings_recipienttitle         = get_option( 'tmsm_woocommerce_vouchers_recipienttitle' ) == 'yes';
			$settings_recipienttitlerequired = get_option( 'tmsm_woocommerce_vouchers_recipienttitlerequired' ) == 'yes';
			$submit_recipienttitle           = isset( $_POST['_recipienttitle'][ $variation_id ] )
				? sanitize_text_field( $_POST['_recipienttitle'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipienttitlerequired && empty( $submit_recipienttitle ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient title', 'tmsm-woocommerce-vouchers' ) . ' ' . __( 'is required.',
						'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// firstname
			$settings_recipientfirstname         = get_option( 'tmsm_woocommerce_vouchers_recipientfirstname' ) == 'yes';
			$settings_recipientfirstnamerequired = get_option( 'tmsm_woocommerce_vouchers_recipientfirstnamerequired' ) == 'yes';
			$submit_recipientfirstname           = isset( $_POST['_recipientfirstname'][ $variation_id ] )
				? sanitize_text_field( $_POST['_recipientfirstname'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientfirstnamerequired && empty( $submit_recipientfirstname ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient first name', 'tmsm-woocommerce-vouchers' ) . ' '
				               . __( 'is required.', 'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// lastname
			$settings_recipientlastname         = get_option( 'tmsm_woocommerce_vouchers_recipientlastname' ) == 'yes';
			$settings_recipientlastnamerequired = get_option( 'tmsm_woocommerce_vouchers_recipientlastnamerequired' ) == 'yes';
			$submit_recipientlastname           = isset( $_POST['_recipientlastname'][ $variation_id ] )
				? sanitize_text_field( $_POST['_recipientlastname'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientlastnamerequired && empty( $submit_recipientlastname ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient last name', 'tmsm-woocommerce-vouchers' ) . ' '
				               . __( 'is required.', 'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// birthdate
			$settings_recipientbirthdate         = get_option( 'tmsm_woocommerce_vouchers_recipientbirthdate' ) == 'yes';
			$settings_recipientbirthdaterequired = get_option( 'tmsm_woocommerce_vouchers_recipientbirthdaterequired' ) == 'yes';
			$submit_recipientbirthdate           = isset( $_POST['_recipientbirthdate'][ $variation_id ] )
				? sanitize_text_field( $_POST['_recipientbirthdate'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientbirthdaterequired && empty( $submit_recipientbirthdate ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient birth date', 'tmsm-woocommerce-vouchers' ) . ' '
				               . __( 'is required.', 'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// address
			$settings_recipientaddress         = get_option( 'tmsm_woocommerce_vouchers_recipientaddress' ) == 'yes';
			$settings_recipientaddressrequired = get_option( 'tmsm_woocommerce_vouchers_recipientaddressrequired' ) == 'yes';
			$submit_recipientaddress           = isset( $_POST['_recipientaddress'][ $variation_id ] )
				? sanitize_text_field( $_POST['_recipientaddress'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientaddressrequired && empty( $submit_recipientaddress ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient address', 'tmsm-woocommerce-vouchers' ) . ' '
				               . __( 'is required.', 'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// zipcode
			$settings_recipientzipcode         = get_option( 'tmsm_woocommerce_vouchers_recipientzipcode' ) == 'yes';
			$settings_recipientzipcoderequired = get_option( 'tmsm_woocommerce_vouchers_recipientzipcoderequired' ) == 'yes';
			$submit_recipientzipcode           = isset( $_POST['_recipientzipcode'][ $variation_id ] )
				? sanitize_text_field( $_POST['_recipientzipcode'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientzipcoderequired && empty( $submit_recipientzipcode ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient zipcode', 'tmsm-woocommerce-vouchers' ) . ' '
				               . __( 'is required.', 'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// city
			$settings_recipientcity         = get_option( 'tmsm_woocommerce_vouchers_recipientcity' ) == 'yes';
			$settings_recipientcityrequired = get_option( 'tmsm_woocommerce_vouchers_recipientcityrequired' ) == 'yes';
			$submit_recipientcity           = isset( $_POST['_recipientcity'][ $variation_id ] )
				? sanitize_text_field( $_POST['_recipientcity'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientcityrequired && empty( $submit_recipientcity ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient city', 'tmsm-woocommerce-vouchers' ) . ' ' . __( 'is required.',
						'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// country
			$settings_recipientcountry         = get_option( 'tmsm_woocommerce_vouchers_recipientcountry' ) == 'yes';
			$settings_recipientcountryrequired = get_option( 'tmsm_woocommerce_vouchers_recipientcountryrequired' ) == 'yes';
			$submit_recipientcountry           = isset( $_POST['_recipientcountry'][ $variation_id ] )
				? sanitize_text_field( $_POST['_recipientcountry'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientcountryrequired && empty( $submit_recipientcountry ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient country', 'tmsm-woocommerce-vouchers' ) . ' '
				               . __( 'is required.', 'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// mobilephone
			$settings_recipientmobilephone         = get_option( 'tmsm_woocommerce_vouchers_recipientmobilephone' ) == 'yes';
			$settings_recipientmobilephonerequired = get_option( 'tmsm_woocommerce_vouchers_recipientmobilephonerequired' ) == 'yes';
			$submit_recipientmobilephone           = isset( $_POST['_recipientmobilephone'][ $variation_id ] )
				? sanitize_text_field( $_POST['_recipientmobilephone'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientmobilephonerequired && empty( $submit_recipientmobilephone ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient phone', 'tmsm-woocommerce-vouchers' ) . ' ' . __( 'is required.',
						'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// email
			$settings_recipientemail         = get_option( 'tmsm_woocommerce_vouchers_recipientemail' ) == 'yes';
			$settings_recipientemailrequired = get_option( 'tmsm_woocommerce_vouchers_recipientemailrequired' ) == 'yes';
			$submit_recipientemail           = isset( $_POST['_recipientemail'][ $variation_id ] )
				? sanitize_text_field( $_POST['_recipientemail'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientemailrequired && empty( $submit_recipientemail ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient email', 'tmsm-woocommerce-vouchers' ) . ' ' . __( 'is required.',
						'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			} elseif ( ! empty( $submit_recipientemail ) && ! is_email( $submit_recipientemail ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Please enter valid email', 'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
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
	 * @param array   $cart_item_data
	 * @param integer $product_id
	 * @param integer $variation_id
	 *
	 * @return array $cart_item_data
	 */
	public function woocommerce_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {

		$variation_id = $variation_id ? $variation_id : $product_id;

		// title
		$submit_recipienttitle             = isset( $_POST['_recipienttitle'][ $variation_id ] )
			? trim(sanitize_text_field( $_POST['_recipienttitle'][ $variation_id ] )) : '';
		if(!empty($submit_recipienttitle)){
			$cart_item_data['_recipienttitle'] = $submit_recipienttitle;
		}

		// firstname
		$submit_recipientfirstname             = isset( $_POST['_recipientfirstname'][ $variation_id ] )
			? trim(sanitize_text_field( $_POST['_recipientfirstname'][ $variation_id ] )) : '';
		if(!empty($submit_recipientfirstname)){
			$cart_item_data['_recipientfirstname'] = $submit_recipientfirstname;
		}

		// lastname
		$submit_recipientlastname             = isset( $_POST['_recipientlastname'][ $variation_id ] )
			? trim(sanitize_text_field( $_POST['_recipientlastname'][ $variation_id ] )) : '';
		if(!empty($submit_recipientlastname)){
			$cart_item_data['_recipientlastname'] = $submit_recipientlastname;
		}

		// birthdate
		$submit_recipientbirthdate            = isset( $_POST['_recipientbirthdate'][ $variation_id ] )
			? trim(sanitize_text_field( $_POST['_recipientbirthdate'][ $variation_id ] )) : '';
		if(!empty($submit_recipientbirthdate)){
			$cart_item_data['_recipientbirthdate'] = $submit_recipientbirthdate;
		}

		// address
		$submit_recipientaddress             = isset( $_POST['_recipientaddress'][ $variation_id ] )
			? trim(sanitize_text_field( $_POST['_recipientaddress'][ $variation_id ] )) : '';
		if(!empty($submit_recipientaddress)){
			$cart_item_data['_recipientaddress'] = $submit_recipientaddress;
		}

		// zipcode
		$submit_recipientzipcode             = isset( $_POST['_recipientzipcode'][ $variation_id ] )
			? trim(sanitize_text_field( $_POST['_recipientzipcode'][ $variation_id ] )) : '';
		if(!empty($submit_recipientzipcode)){
			$cart_item_data['_recipientzipcode'] = $submit_recipientzipcode;
		}

		// city
		$submit_recipientcity             = isset( $_POST['_recipientcity'][ $variation_id ] )
			? trim(sanitize_text_field( $_POST['_recipientcity'][ $variation_id ] )) : '';
		if(!empty($submit_recipientcity)){
			$cart_item_data['_recipientcity'] = $submit_recipientcity;
		}

		// country
		$submit_recipientcountry             = isset( $_POST['_recipientcountry'][ $variation_id ] )
			? trim(sanitize_text_field( $_POST['_recipientcountry'][ $variation_id ] )) : '';
		if(!empty($submit_recipientcountry)){
			$cart_item_data['_recipientcountry'] = $submit_recipientcountry;
		}

		// mobilephone
		$submit_recipientmobilephone             = isset( $_POST['_recipientmobilephone'][ $variation_id ] )
			? trim(sanitize_text_field( $_POST['_recipientmobilephone'][ $variation_id ] )) : '';
		if(!empty($submit_recipientmobilephone)){
			$cart_item_data['_recipientmobilephone'] = $submit_recipientmobilephone;
		}

		// email
		$submit_recipientemail             = isset( $_POST['_recipientemail'][ $variation_id ] )
			? trim(sanitize_text_field( $_POST['_recipientemail'][ $variation_id ] )) : '';
		if(!empty($submit_recipientemail)){
			$cart_item_data['_recipientemail'] = $submit_recipientemail;
		}

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
	public function woocommerce_get_item_data( $data, $item ) {

		$product_id = isset( $item['product_id'] ) ? $item['product_id'] : '';

		if ( ! empty( $item['_recipientlastname'] ) && ! empty( $item['_recipientfirstname'] ) ) {
			switch ( $item['_recipienttitle'] ) {
				case 1:
					$title = __( 'Mr', 'tmsm-woocommerce-vouchers' ) . ' ';
					break;
				case 2:
					$title = __( 'Ms', 'tmsm-woocommerce-vouchers' ) . ' ';
					break;
				default:
					$title = '';
					break;

			}
			$recipient = array(
				'first_name' => $title . $item['_recipientfirstname'],
				'last_name'  => ( ! empty( $item['_recipientlastname'] ) ? $item['_recipientlastname'] : '' ),
				'address_1'  => ( ! empty( $item['_recipientaddress'] ) ? $item['_recipientaddress'] : '' ),
				//'address_2'   => '',
				'city'       => ( ! empty( $item['_recipientcity'] ) ? $item['_recipientcity'] : '' ),
				'postcode'   => ( ! empty( $item['_recipientzipcode'] ) ? $item['_recipientzipcode'] : '' ),
				//'state'    => '',
				'country'    => ( ! empty( $item['_recipientcountry'] ) ? $item['_recipientcountry'] : '' ),
			);

			$formatted_recipient = WC()->countries->get_formatted_address( $recipient );
			$data[]              = array(
				'name'    => __( 'Recipient', 'tmsm-woocommerce-vouchers' ),
				'display' => $formatted_recipient,
				'hidden'  => false,
				'value'   => '',
			);
		}

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
	public function woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {

		$variation_id = isset( $values['variation_id'] ) && ! empty( $values['variation_id'] ) ? $values['variation_id'] : $values['product_id'];

		$product = $item->get_product();

		if ( $product ) {
			$variation_id = $product->get_id();
		}

		$is_virtual = get_post_meta( $variation_id, '_virtual', true ) == 'yes';
		$is_voucher = get_post_meta( $variation_id, '_voucher', true ) == 'yes';

		if ( $is_virtual ) {
			$item->add_meta_data( '_virtual', 'yes', true );
		} else {
			$item->add_meta_data( '_virtual', 'no', true );
		}

		if ( $is_voucher ) {
			$item->add_meta_data( '_voucher', 'yes', true );
		}

		if ( ! empty( $values['_recipienttitle'] ) ) {
			$item->add_meta_data( '_recipienttitle', $values['_recipienttitle'], true );
		}

		if ( ! empty( $values['_recipientfirstname'] ) ) {
			$item->add_meta_data( '_recipientfirstname', $values['_recipientfirstname'], true );
		}

		if ( ! empty( $values['_recipientlastname'] ) ) {
			$item->add_meta_data( '_recipientlastname', $values['_recipientlastname'], true );
		}

		if ( ! empty( $values['_recipientbirthdate'] ) ) {
			if( method_exists('DateTime', 'createFromFormat') ){
				$objdate = DateTime::createFromFormat( _x( 'm/d/Y', 'birth date format conversion', 'tmsm-woocommerce-vouchers' ),
					sanitize_text_field( $values['_recipientbirthdate'] ) );
				if( $objdate instanceof DateTime ){
					$item->add_meta_data( '_recipientbirthdate', sanitize_text_field( $objdate->format('Y-m-d') ), true );
				}
			}
		}

		if ( ! empty( $values['_recipientaddress'] ) ) {
			$item->add_meta_data( '_recipientaddress', $values['_recipientaddress'], true );
		}

		if ( ! empty( $values['_recipientzipcode'] ) ) {
			$item->add_meta_data( '_recipientzipcode', $values['_recipientzipcode'], true );
		}

		if ( ! empty( $values['_recipientcity'] ) ) {
			$item->add_meta_data( '_recipientcity', $values['_recipientcity'], true );
		}

		if ( ! empty( $values['_recipientcountry'] ) ) {
			$item->add_meta_data( '_recipientcountry', $values['_recipientcountry'], true );
		}

		if ( ! empty( $values['_recipientmobilephone'] ) ) {
			$item->add_meta_data( '_recipientmobilephone', $values['_recipientmobilephone'], true );
		}

		if ( ! empty( $values['_recipientemail'] ) ) {
			$item->add_meta_data( '_recipientemail', $values['_recipientemail'], true );
		}
		error_log(print_r($item, true));

	}


	/**
	 * Displays recipient data date for order item in order view (frontend)
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
	public function woocommerce_display_item_meta( $html, $item, $args ) {

		$this->woocommerce_payment_complete($item->get_order_id());

		$strings = [];

		if ( !empty($item['_recipientlastname']) && !empty($item['_recipientfirstname'] )) {
			switch ( $item['_recipienttitle'] ) {
				case 1:
					$title = __( 'Mr', 'tmsm-woocommerce-vouchers' ) . ' ';
					break;
				case 2:
					$title = __( 'Ms', 'tmsm-woocommerce-vouchers' ) . ' ';
					break;
				default:
					$title = '';
					break;
			}

			$recipient = array(
				'first_name' => $title . $item->get_meta( '_recipientfirstname' ),
				'last_name'  => $item->get_meta( '_recipientlastname' ),
				'address_1'  => $item->get_meta( '_recipientaddress' ),
				//'address_2'   => '',
				'city'       => $item->get_meta( '_recipientcity' ),
				'postcode'   => $item->get_meta( '_recipientzipcode' ),
				//'state'    => '',
				'country'    => $item->get_meta( '_recipientcountry' ),
			);

			$formatted_recipient = WC()->countries->get_formatted_address( $recipient );
			$strings[]           = '<strong class="wc-item-meta-label">' . __( 'Recipient:', 'tmsm-woocommerce-vouchers' ) . '</strong> '
			                       . $formatted_recipient;
		}

		if ( !empty($item['_recipientbirthdate'])) {
			$strings[]           = '<strong class="wc-item-meta-label">' . __( 'Birth date:', 'tmsm-woocommerce-vouchers' ) . '</strong> '
			                       . date_i18n( get_option( 'date_format' ), strtotime( $item['_recipientbirthdate'] ) );
		}

		if ( !empty($item['_vouchercode'])) {
			$strings[]           = '<strong class="wc-item-meta-label">' . __( 'Voucher code:', 'tmsm-woocommerce-vouchers' ) . '</strong> '
			                       . $item['_vouchercode'];
		}


		if ( !empty($item['_expirydate'])) {
			$strings[]           = '<strong class="wc-item-meta-label">' . __( 'Expiry date:', 'tmsm-woocommerce-vouchers' ) . '</strong> '
			                       . date_i18n( get_option( 'date_format' ), strtotime( $item['_expirydate'] ) );
		}

		if ( $strings != [] ) {
			$html .= $args['before'] . implode( $args['separator'], $strings ) . $args['after'];
		}

		return $html;
	}



	/**
	 * Create download links in /my-account/downloads/ page
	 *
	 * @since 1.0.0
	 *
	 * @param $order_id
	 *
	 * @return void
	 */
	public function woocommerce_grant_product_download_permissions( $order_id ) {

		$this->woocommerce_payment_complete($order_id);

		$order = wc_get_order( $order_id );

		$downloadable_files = [];

		if ( sizeof( $order->get_items() ) > 0 ) { //Get all items in order
			foreach ( $order->get_items() as $item_id => $item ) {

				$product = $item->get_product();
				//$_product = $order->get_product_from_item($item); //Get product from Item ( It is required otherwise multipdf voucher link not work and global $woo_vou_item_id will not work )
				$variation_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : ''; // Taking variation id

				if ( $product && $product->exists() ) { // && $_product->is_downloadable()

					$product_id = $product->get_id();
					$data_id    = ( ! empty( $variation_id ) ) ? $variation_id : $product_id;

					$enable_voucher = $this->tmsmvoucher_product_type_is_voucher_virtual($product_id, $variation_id);
					if ( $enable_voucher ) {

						$downloadable_files = $this->tmsmvoucher_download_key( $order_id, $data_id, $item_id );

						foreach ( array_keys( $downloadable_files ) as $download_id ) {
							wc_downloadable_file_permission( $download_id, $data_id, $order );
						}
					}
				}
			}
		}

	}


	/**
	 * Add downloadable files for WooCommerce version >= 3.0
	 *
	 * @since  1.0.0
	 *
	 * @param array                 $files
	 * @param WC_Order_Item_Product $item
	 * @param WC_Order              $order
	 *
	 * @return array $files
	 */
	public function woocommerce_get_item_downloads( $files, $item, $order ) {

		//echo 'woocommerce_get_item_downloads tmsm';
		$this->woocommerce_payment_complete($order->get_id());

		$product = $item->get_product();

		if ( ! ( $product && $order && $product->is_downloadable() && $order->is_download_permitted() ) ) {
			return $files;
		}

		$variation_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];

		$pdf_downloadable_files = $this->tmsmvoucher_download_key( $order->get_id(), $variation_id, $item->get_id() );

		if ( ! empty( $pdf_downloadable_files ) ) {
			//print_r('not empty $pdf_downloadable_files');
			foreach ( $pdf_downloadable_files as $pdf_key => $pdf_file_array ) {

				// Add download url to voucher downlodable files
				$pdf_downloadable_files[ $pdf_key ]['download_url'] = $item->get_item_download_url( $pdf_key );
				$pdf_downloadable_files[ $pdf_key ]['test'] = $item->get_item_download_url( $pdf_key );
				$pdf_downloadable_files[ $pdf_key ]['downloads_remaining'] = 'unlimited';
				$pdf_downloadable_files[ $pdf_key ]['access_expires'] = null;
				$pdf_downloadable_files[ $pdf_key ]['id'] = $item->get_id();

				// Merge downlodable file to files
				$files = array_merge( $files, array( $pdf_key => $pdf_downloadable_files[ $pdf_key ] ) );
			}
		}

		// Add item id in download pdf url
		if ( ! empty( $files ) ) { //If files not empty
			//print_r('not empty $files');
			foreach ( $files as $file_key => $file_data ) {

				//print_r($file_data);
				//Check key is for pdf voucher
				$check_key = strpos( $file_key, 'tmsmvoucher' );

				if ( $check_key !== false ) {

					//Get download URL
					$download_url = isset( $files[ $file_key ]['download_url'] ) ? $files[ $file_key ]['download_url'] : '';

					//Add item id in download URL
					$download_url = add_query_arg( array( 'item_id' => $item->get_id() ), $download_url );

					//Store download URL agaiin
					$files[ $file_key ]['download_url'] = $download_url;
				}
			}
		}

		//print_r($files);


		return $files;

	}

	/**
	 * Gets a customer's downloadable products
	 *
	 * @since 1.0.0
	 *
	 * @param array $downloads
	 *
	 * @return array $downloads
	 * @throws Exception
	 */
	public function woocommerce_customer_get_downloadable_products( $downloads = [] ) {

		if ( is_user_logged_in() ) {//If user is logged in
			//Get user ID
			$user_id = get_current_user_id();

			//Get User Order Arguments
			$args = array(
				'numberposts' => - 1,
				'meta_key'    => '_customer_user',
				'meta_value'  => $user_id,
				'post_type'   => wc_get_order_types( 'view-orders' ),
				'post_status' => wc_get_order_statuses(),
			);

			//user orders
			$user_orders = get_posts( $args );

			if ( ! empty( $user_orders ) ) {//If orders are not empty
				foreach ( $user_orders as $user_order ) {
					//Get order ID
					$order_id = isset( $user_order->ID ) ? $user_order->ID : '';

					if ( ! empty( $order_id ) ) {//Order it not empty


						//Get cart details
						$order = wc_get_order( $order_id );
						$order_items  = $order->get_items();
						$order_date   = $order->get_date_modified(); // Get order date
						//$order_date   = date( 'F j, Y', strtotime( $order_date ) );

						if ( ! empty( $order_items ) ) {// Check cart details are not empty
							foreach ( $order_items as $item_id => $item ) {

								if ( is_callable( array( $item, 'get_product' ) ) ) {
									$_product = $item->get_product();
								}
								else{
									$_product = $order->get_product_from_item( $item );
								}

								if ( ! $_product ) {//If product deleted
									$download_file_data = array();
								} else {
									//$download_file_data = $woo_vou_model->woo_vou_get_item_downloads_from_order( $order, $item );
									$download_file_data = $item->get_item_downloads();
								}

								//Get voucher codes
								$code = wc_get_order_item_meta( $item_id, '_vouchercode', true );

								$expirydate = wc_get_order_item_meta( $item_id, '_expirydate', true );

								if ( ! empty( $download_file_data ) && ! empty( $code ) ) {//If download exist and code is not empty
									foreach ( $download_file_data as $key => $download_file ) {

										//check download key is voucher key or not
										$check_key = strpos( $key, 'tmsmvoucher_pdf_'.$item_id.'_' );

										//get voucher number
										$voucher_number = str_replace( 'tmsmvoucher_pdf_'.$item_id.'_', '', $key );
										
										if ( empty( $voucher_number ) ) {//If empty voucher number
											$voucher_number = 1;
										}

										if ( ! empty( $download_file ) && $check_key !== false ) {

											//Get download URL
											$download_url = $download_file['download_url'];

											//add arguments array
											$add_arguments = array( 'item_id' => $item_id );

											//PDF Download URL
											$download_url = add_query_arg( $add_arguments, $download_url );
											//Get product ID
											$product_id = $_product->get_id();


											switch ( $item->get_meta('_recipienttitle') ) {
												case 1:
													$title = __( 'Mr', 'tmsm-woocommerce-vouchers' ) . ' ';
													break;
												case 2:
													$title = __( 'Ms', 'tmsm-woocommerce-vouchers' ) . ' ';
													break;
												default:
													$title = '';
													break;
											}

											$recipient = array(
												'first_name' => $title . $item->get_meta( '_recipientfirstname' ),
												'last_name'  => $item->get_meta( '_recipientlastname' ),
											);

											$formatted_recipient = WC()->countries->get_formatted_address( $recipient );

											$voucher_codes = explode(', ', $code);
											$voucher_code = null;
											if( isset( $voucher_codes[($voucher_number - 1)] ) ) {
												$voucher_code = $voucher_codes[($voucher_number - 1)];
											}

											// Download file arguments
											$download_args = array(
												'product_id'          => $product_id,
												'product_name'        => $item->get_name() . ' '. __( 'for', 'tmsm-woocommerce-vouchers' ) . ' ' .$formatted_recipient,
												'download_url'        => $download_url,
												'product_url'        => '',
												//'download_name'       => $_product->get_title() . $download_file['name'] . ' ' . $voucher_number . ' ( ' . $order_date . ' )',
												'download_name'       => $download_file['name']. ' '.$voucher_code,
												'access_expires'       => $expirydate,

												'downloads_remaining' => 'unlimited',
												'file'                => array(
													'name' => $download_file['name'],
													'file' => $download_file['file'],
												),
											);

											//append voucher download to downloads array
											$downloads[] = $download_args;
										}
									}
								}
							}
						}

					}
				}
			}
		}


		return $downloads;
	}


	/**
	 * Download Process
	 *
	 * @since 1.0.0
	 *
	 * @param $email
	 * @param $order_key
	 * @param $product_id
	 * @param $user_id
	 * @param $download_id
	 * @param $order_id
	 * @throws Exception
	 */
	function woocommerce_download_product( $email, $order_key, $product_id, $user_id, $download_id, $order_id ) {

		$order = wc_get_order_id_by_order_key($order_key);


		if(empty($order_key) || empty($order)){
			die(__( 'You are not allowed to download this file', 'tmsm-woocommerce-vouchers' ));
		}

		$item_id = wc_clean( $_GET['item_id'] );

		$pdf_filename = get_option('tmsm_woocommerce_vouchers_downloadfilename');
		$pdf_filename = isset($pdf_filename) ? $pdf_filename : 'voucher-{current_date}-{unique_string}';
		$pdf_filename = str_replace('{item_id}', $item_id, $pdf_filename);
		$pdf_filename = str_replace('{product_id}', $product_id, $pdf_filename);
		$pdf_filename = str_replace('{order_id}', $order_id, $pdf_filename);
		$pdf_filename = str_replace('{current_date}',  date('Ymd'), $pdf_filename);
		$pdf_filename = str_replace('{unique_string}',  $this->tmsmvoucher_generate_uniquestring(), $pdf_filename);
		$pdf_filename .= '.pdf';

		$download_key = wc_clean( $_GET['key'] );
		$download_key = str_replace('tmsmvoucher_pdf_'.$item_id.'_','',$download_key);
		$download_key = absint( $download_key );

		if ( !is_int( $download_key) || !$download_key ){
			die( __( 'You are not allowed to download this file', 'tmsm-woocommerce-vouchers' ) );
		}

		$pdf_args = [
			'pdf_filepath' => $pdf_filename,
			'download_key' => $download_key
		];
		if ( ! empty( $item_id ) ) {
			$this->tmsmvoucher_voucher_html_template( $product_id, $order_id, $item_id, $pdf_args );
		}
		exit;
	}


	/**
	 * Handles the functionality to attach the voucher pdf in mail
	 *
	 * @see woo_vou_attach_voucher_to_email
	 *
	 * @param array $attachments
	 * @param string $status
	 * @param WC_Order $order
	 *
	 * @return array $attachments
	 * @throws Exception
	 */
	public function woocommerce_email_attachments($attachments, $status, $order){
		global $post;

		$order_id = '';
		$order_status = '';

		$processing_status = ['customer_processing_order', 'customer_completed_order', 'customer_invoice'];
		$completed_status = ['wc-completed', 'completed', 'wc-processed', 'processed'];

		if (is_array($order)) { // If order is an array
			if (isset($order['order_id'])) { // If order_id is set in order array ( Happens when order is created through REST )
				$order_id = $order['order_id'];
			} else if(is_object($post)) {

				$order_id = $post->ID;
			}
			if($order_id){
				$_order = wc_get_order($order_id);
				$order_status = $_order->get_status();
			}
		} else if (is_object($order)) {
			$order_status = $order->get_status();
			$order_id = $order->get_id();
		}

		$settings_attachemail = get_option( 'tmsm_woocommerce_vouchers_attachemail' );
		$grant_access_after_payment = get_option('woocommerce_downloads_grant_access_after_payment'); // Woocommerce grant access after payment

		if ($order_id && $settings_attachemail == 'yes' && !empty($order) && ( (in_array($status, $processing_status) && in_array($order_status, $completed_status)) || ($status == 'customer_processing_order' && $grant_access_after_payment == 'yes' && $order_status != 'wc-on-hold') )) {

			$this->woocommerce_payment_complete($order_id);

			$cart_details = wc_get_order($order_id);
			$order_items = $cart_details->get_items();

			if (!empty($order_items)) {//not empty items
				//foreach items
				foreach ($order_items as $item_id => $download_data) {

					$product_id = !empty($download_data['product_id']) ? $download_data['product_id'] : '';
					$variation_id = !empty($download_data['variation_id']) ? $download_data['variation_id'] : '';

					//Get data id vriation id or product id
					$data_id = !empty($variation_id) ? $variation_id : $product_id;

					//Check voucher enable or not
					$enable_voucher = $this->tmsmvoucher_product_type_is_voucher_virtual($product_id, $variation_id);

					if ($enable_voucher) {

						$order_codes = [];

						$order_codes = $this->tmsmvoucher_get_multi_voucher($order_id, $data_id, $item_id);

						if (!empty($order_codes)) {

							foreach ($order_codes as $order_codes_key => $order_codes_val) {

								if (!empty($order_codes_key)) {

									$pdf_filename = get_option('tmsm_woocommerce_vouchers_attachmentfilename');
									$pdf_filename = isset($pdf_filename) ? $pdf_filename : 'voucher-{current_date}-{unique_string}';
									$pdf_filename = str_replace('{item_id}', $item_id, $pdf_filename);
									$pdf_filename = str_replace('{product_id}', $data_id, $pdf_filename);
									$pdf_filename = str_replace('{order_id}', $order_id, $pdf_filename);
									$pdf_filename = str_replace('{voucher_code}', $order_codes_val, $pdf_filename);
									$pdf_filename = str_replace('{current_date}',  date('Ymd'), $pdf_filename);
									$pdf_filename = str_replace('{purchase_date}',  $order->get_date_paid(), $pdf_filename);
									$pdf_filename = str_replace('{unique_string}',  $this->tmsmvoucher_generate_uniquestring(), $pdf_filename);

									// Voucher pdf path and voucher name
									$pdf_filepath = TMSMWOOCOMMERCEVOUCHERS_UPLOADDIR . $pdf_filename . '.pdf'; // Voucher pdf path

									$download_key = str_replace('tmsmvoucher_pdf_'.$item_id.'_','',$order_codes_key);

									// If voucher pdf does not exist in folder
									if (!file_exists($pdf_filepath)) {

										$pdf_args = array(
											'pdf_filepath' => $pdf_filepath,
											'pdf_save' => true,
											'download_key' => $download_key,
										);
										//Generating pdf
										$this->tmsmvoucher_voucher_html_template( $data_id, $order_id, $item_id, $pdf_args);
									}

									// If voucher pdf exist in folder
									if (file_exists($pdf_filepath)) {
										$attachments[] = $pdf_filepath; // Adding the voucher pdf in attachment array
									}
								}
							}
						}
					}
				}
			} // End of order item
		}
		return $attachments;
	}

	/**
	 * Theme has parent theme
	 *
	 * @param $theme_name
	 *
	 * @return bool
	 */
	function has_parent_theme( $theme_name ) {
		$current_theme = wp_get_theme();
		$parent_theme  = $current_theme->parent();

		return ! empty( $parent_theme ) && ! empty( $parent_theme->name ) && $parent_theme->name === $theme_name;
	}

	/**
	 *  Single product display voucher expire in meta
	 */
	public function woocommerce_product_meta_end(){
		global $product;

		$gift_icon        = null;
		$shipping_icon    = null;
		$payment_icon     = null;
		$localpickup_icon = null;

		if ( self::has_parent_theme( 'StormBringer' ) ) {
			$gift_icon        = 'glyphicon glyphicon-gift';
			$shipping_icon    = '';
			$payment_icon     = 'glyphicon glyphicon-credit-card';
			$localpickup_icon = 'glyphicon glyphicon-map-marker';
		}
		if ( self::has_parent_theme( 'OceanWP' ) || wp_style_is( 'font-awesome', 'registered' ) ) {
			$gift_icon        = 'fa fa-gift';
			$shipping_icon    = 'fa fa-truck';
			$payment_icon     = 'fa fa-credit-card';
			$localpickup_icon = 'fa fa-map-marker';
		}

		if(!empty($product) ){

			$has_variations = sizeof( $product->get_children() );
			$has_voucher = false;
			$expiremonths = 0;

			if($has_variations) {
				$variations = $product->get_available_variations();

				if(!empty($variations)){
					foreach($variations as $variation){

						$variation_id = $variation['variation_id'];
						if(!empty($variation_id)){
							$variation_product = wc_get_product($variation_id);
							if(!empty($variation_product)){
								$voucher      = get_post_meta( $variation_product->get_id(), '_voucher', true );
								if($voucher === 'yes'){
									$has_voucher = true;
								}
							}
						}
					}
				}
			}
			else{
				$voucher      = get_post_meta( $product->get_id(), '_voucher', true );

				if($voucher === 'yes'){
					$has_voucher = true;
				}
			}

			$expiremonths   = get_post_meta( $product->get_id(), '_tmsm_woocommerce_vouchers_expiremonths', true );
			$expirydate   = get_post_meta( $product->get_id(), '_tmsm_woocommerce_vouchers_expirydate', true );

			// Expiry Date has to be in the future
			if(!empty($expirydate) && $expirydate < date('Y-m-d')){
				$expirydate = null;
			}
			if(empty($expiremonths)){
				$expiremonths = get_option( 'tmsm_woocommerce_vouchers_expiremonths' );
			}

			if($has_voucher ){
				if(!empty($expirydate)){
					echo '<p class="product_meta_voucher"><span class="'.$gift_icon.'"></span> <time datetime="'.date( 'Y-m-d', strtotime( $expirydate ) ).'" title="'.esc_attr( strtotime( $expirydate ) ).'">'. sprintf( __( 'Voucher valid until %s', 'tmsm-woocommerce-vouchers' ), date_i18n( get_option( 'date_format' ), strtotime( $expirydate ) )).'</time></p>';
				}
				else{
					echo '<p class="product_meta_voucher"><span class="'.$gift_icon.'"></span> '.sprintf( _n( 'Voucher valid %s month', 'Voucher valid %s months', $expiremonths, 'tmsm-woocommerce-vouchers' ), $expiremonths ).'</p>';
				}
			}

		}
	}


	/**
	 * Generates a unique string
	 *
	 * @param integer $nb_of_chars
	 *
	 * @return string $string
	 */
	private function tmsmvoucher_generate_uniquestring($nb_of_chars = 8){
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$string = '';
		$max = strlen($characters) - 1;
		for ($i = 0; $i < $nb_of_chars; $i++) {
			$string .= $characters[mt_rand(0, $max)];
		}
		return $string;
	}

	/**
	 * Check if product/variation has voucher physical enabled
	 *
	 * @param $product_id
	 * @param $variation_id
	 *
	 * @return bool $enable_voucher
	 */
	private function tmsmvoucher_product_type_is_voucher_physical( $product_id, $variation_id ) {
		$enable_voucher = false;

		$data_id = ( ! empty( $variation_id ) ? $variation_id : $product_id );

		$voucher      = get_post_meta( $data_id, '_voucher', true );
		$virtual      = get_post_meta( $data_id, '_virtual', true );
		$downloadable = get_post_meta( $data_id, '_downloadable', true );
		$settings_physical = get_option( 'tmsm_woocommerce_vouchers_physical' );

		if ( $voucher =='yes' && $virtual == 'no' && $downloadable == 'no' && $settings_physical == 'yes' ) {
			$enable_voucher = true;
		}

		return $enable_voucher;
	}

	/**
	 * Check if product/variation has voucher virtual enabled
	 *
	 * @param $product_id
	 * @param $variation_id
	 *
	 * @return bool $enable_voucher
	 */
	private function tmsmvoucher_product_type_is_voucher_virtual( $product_id, $variation_id ) {
		$enable_voucher = false;

		$data_id = ( ! empty( $variation_id ) ? $variation_id : $product_id );

		$voucher      = get_post_meta( $data_id, '_voucher', true );
		$virtual      = get_post_meta( $data_id, '_virtual', true );
		$downloadable = get_post_meta( $data_id, '_downloadable', true );
		$settings_virtual  = get_option( 'tmsm_woocommerce_vouchers_virtual' );

		if ( $voucher == 'yes' && $virtual == 'yes' && $downloadable == 'yes' && $settings_virtual == 'yes' ) {
			$enable_voucher = true;
		}

		return $enable_voucher;
	}

	/**
	 * Generate unique code
	 *
	 * @since 1.0.0
	 *
	 * @param integer $order_id
	 * @param integer $product_id
	 * @param integer $variation_id
	 *
	 * @return string
	 */
	private function tmsmvoucher_generate_code( $order_id, $product_id, $variation_id ) {


		$product = wc_get_product((!empty($variation_id) ? $variation_id : $product_id));

		$is_virtual = get_post_meta( $product->get_id(), '_virtual', true ) == 'yes';

		if($is_virtual){
			$voucheruses = $product->get_meta('_voucheruses', true );
			if(empty($voucheruses)){
				$voucheruses = 0;
			}

			$settings_vouchercodeformat  = get_option( 'tmsm_woocommerce_vouchers_vouchercodeformat' );

			if(empty($settings_vouchercodeformat)){
				$settings_vouchercodeformat = '{sku}-{uses}';
			}

			$settings_vouchercodeformat = str_replace('{order_id}', $order_id, $settings_vouchercodeformat);
			$settings_vouchercodeformat = str_replace('{sku}', $product->get_sku(), $settings_vouchercodeformat);
			$settings_vouchercodeformat = str_replace('{uses}', str_pad(($voucheruses + 1), 5, '0', STR_PAD_LEFT), $settings_vouchercodeformat);

			$code = $settings_vouchercodeformat;

			$product->add_meta_data('_voucheruses' , ($voucheruses + 1), true);
			$product->save();
		}
		else{
			$code = '';
		}

		return $code;
	}

	/**
	 * Get downloadable vouchers files
	 *
	 * @since 1.0.0
	 *
	 * @param string $order_id
	 * @param string $product_id
	 * @param string $item_id
	 *
	 * @return array
	 */
	private function tmsmvoucher_download_key( $order_id = '', $product_id = '', $item_id = '' ) {

		$downloadable_files = [];

		if ( ! empty( $order_id ) ) {

			$vouchercodes	= $this->tmsmvoucher_get_multi_voucher_key( $order_id, $product_id, $item_id );

			foreach ( $vouchercodes as $codes ) {

				$downloadable_files[$codes] = array(
					'name' => __( 'Download voucher', 'tmsm-woocommerce-vouchers' ),
					'file' => get_permalink( $product_id )
				);
			}

		}

		return $downloadable_files;
	}

	/**
	 * Get Voucher Keys
	 *
	 * Handles to get voucher keys
	 *
	 * @package WooCommerce - PDF Vouchers
	 * @since 1.1.0
	 *
	 * @param string $order_id
	 * @param string $product_id
	 * @param string $item_id
	 *
	 * @return array
	 */
	private function tmsmvoucher_get_multi_voucher_key( $order_id = '', $product_id = '', $item_id = '' ) {

		$voucher_keys	= array();
		$vouchers		= $this->tmsmvoucher_get_multi_voucher( $order_id, $product_id, $item_id );

		if( !empty( $vouchers ) ) {

			$voucher_keys	= array_keys( $vouchers );
		}

		return $voucher_keys;
	}

	/**
	 * Get Vouchers
	 *
	 * Handles to get vouchers
	 *
	 * @package WooCommerce - PDF Vouchers
	 * @since 1.0.0
	 *
	 * @param string $order_id
	 * @param string $product_id
	 * @param string $item_id
	 *
	 * @return array
	 * @throws Exception
	 */
	private function tmsmvoucher_get_multi_voucher( $order_id = '', $product_id = '', $item_id = '' ) {

		$codes = wc_get_order_item_meta( $item_id, '_vouchercode' );
		$codes = ! empty( $codes ) ? explode( ', ', $codes ) : array();
		$vouchers = [];

		if( !empty( $codes ) ) {

			$key	= 1;
			foreach ( $codes as $code ) {

				$vouchers['tmsmvoucher_pdf_'.$item_id.'_'.$key]	= $code;
				$key++;
			}
		}
		return $vouchers;
	}

	/**
	 * Get variation detail from order and item id
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order $order
	 * @param string $item_key
	 *
	 * @return mixed
	 */
	private function tmsmvoucher_get_variation_data($order = array(), $item_key = '') {

		$items = $order->get_items();

		$item_array = $this->tmsmvoucher_get_item_data_using_item_key($items, $item_key);

		$item = isset($item_array['item_data']) ? $item_array['item_data'] : array();
		$item_id = isset($item_array['item_id']) ? $item_array['item_id'] : array();

		//Get product from Item ( It is required otherwise multipdf voucher link not work and global $woo_vou_item_id will not work )
		//$_product = $order->get_product_from_item($item); // obsolete
		$_product = $item->get_product();

		//Get variation data without recipient fields
		$variation_data = $this->tmsmvoucher_display_product_item_name($item, $_product);

		return $variation_data;
	}

	/**
	 * Get Item Data From Item ID
	 *
	 * Handles to get voucher data using
	 * voucher codes from order items
	 *
	 * @package WooCommerce - PDF Vouchers
	 * @since 1.0.0
	 * @param $order_items
	 * @param $item_key
	 *
	 * @return array
	 */
	private function tmsmvoucher_get_item_data_using_item_key( $order_items, $item_key ) {

		//initilize item
		$return_item = array( 'item_id' => '', 'item_data' => array() );

		if (!empty($order_items)) {//if items are not empty
			foreach ($order_items as $item_id => $item) {

				if ($item_key == $item_id) {

					$return_item['item_id'] = $item_id;
					$return_item['item_data'] = $item;
					break;
				}
			}
		}

		return $return_item;
	}

	/**
	 * Display Product Item Name
	 *
	 * Handles to display product item name
	 *
	 * @package WooCommerce - PDF Vouchers
	 * @since 2.2.2
	 *
	 * @param array $item
	 * @param array $product
	 *
	 * @return mixed
	 */
	private function tmsmvoucher_display_product_item_name($item = array(), $product = array()) {

		$product_item_meta = isset($item['item_meta']) ? $item['item_meta'] : array();
		$product_item_name = '';

		$product_id = isset($product_item_meta['_product_id']) ? $product_item_meta['_product_id'] : '';

		if (!empty($product_item_meta)) { // if not empty product meta
			// this is added due to skip depricted function get_formatted_legacy from woocommerce
			if (!defined('DOING_AJAX')) {
				define('DOING_AJAX', true);
			}

			//Item meta object
			$item_meta_object = new WC_Order_Item_Meta($product_item_meta);

			//Get product variations
			$product_variations = $item_meta_object->get_formatted();


			//Get recipient name from old orders
			/*if (!empty($product_item_meta['_recipientfirstname']) && !empty($product_item_meta['_recipientfirstname'][0]) && !is_serialized($product_item_meta['_recipientfirstname'][0])) {

				$product_variations[__('Recipient first name', 'tmsm-woocommerce-vouchers')] = array(
					'label' => __('Recipient first name', 'tmsm-woocommerce-vouchers'),
					'value' => $product_item_meta['_recipientfirstname'][0]
				);
			}

			//Get recipient email from old orders
			if (!empty($product_item_meta['_recipient_email']) && !empty($product_item_meta['_recipient_email'][0]) && !is_serialized($product_item_meta['_recipient_email'][0])) {

				$recipient_email_lbl = $product_recipient_lables['recipient_email_label'];

				$product_variations[$recipient_email_lbl] = array(
					'label' => $recipient_email_lbl,
					'value' => $product_item_meta['_recipient_email'][0]
				);
			}

			//Get recipient message from old orders
			if (!empty($product_item_meta['_recipient_message']) && !empty($product_item_meta['_recipient_message'][0]) && !is_serialized($product_item_meta['_recipient_message'][0])) {

				$recipient_msg_lbl = $product_recipient_lables['recipient_message_label'];

				$product_variations[$recipient_msg_lbl] = array(
					'label' => $recipient_msg_lbl,
					'value' => $product_item_meta['_recipient_message'][0]
				);
			}

			//Get recipient message from old orders
			if (!empty($product_item_meta['_pdf_template_selection']) && !empty($product_item_meta['_pdf_template_selection'][0]) && !is_serialized($product_item_meta['_pdf_template_selection'][0])) {

				$pdf_temp_selection_lbl = $product_recipient_lables['pdf_template_selection_label'];

				$product_variations[$pdf_temp_selection_lbl] = array(
					'label' => $pdf_temp_selection_lbl,
					'value' => $product_item_meta['_pdf_template_selection'][0]
				);
			}*/


			// Create variations Html
			if (!empty($product_variations)) {

				//variation display format
				$variation_param_string = '<br /><strong>%1$s</strong>: %2$s';

				foreach ($product_variations as $product_variation) {
					$product_item_name .= sprintf($variation_param_string, $product_variation['label'], $product_variation['value']);
				}
			}
		}

		return $product_item_name;
	}

	/**
	 * Handles to return product id from product
	 *
	 * Returns parent product id, if variable product is passed
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	private function tmsmvoucher_get_item_productid_from_product($product){

		if ( $product->is_type( 'variable' ) || $product->is_type( 'variation' ) ) {
			$product_id = $product->get_parent_id();
		} else {
			$product_id = $product->get_id();
		}

		return $product_id;
	}


	/**
	 * Generate PDF for Voucher
	 *
	 * Handles to Generate PDF on run time when
	 * user will execute the url which is sent to
	 * user email with purchase receipt
	 *
	 * @since 1.0.0
	 *
	 * @param        $product_id
	 * @param        $order_id
	 * @param string $item_id
	 * @param array  $pdf_args
	 *
	 * @return void
	 * @throws Exception
	 */
	private function tmsmvoucher_voucher_html_template( $product_id, $order_id, $item_id = '', $pdf_args = [] ) {
		global $current_user;

		$product = wc_get_product($product_id);

		if(!empty($product->get_parent_id())){
			$product = wc_get_product($product->get_parent_id());
		}

		$order = wc_get_order($order_id);
		$voucher_code	= wc_get_order_item_meta( $item_id, '_vouchercode', true );

		// Item meta
		$item = WC_Order_Factory::get_order_item( $item_id );
		//$item_meta = wc_display_item_meta($item, ['echo' => false]);
		$item_meta = '';

		$args    = array(
			'before'    => '',
			'after'		=> '<br><br>',
			'separator'	=> '<br>',
			'autop'		=> true,
		);
		$strings = [];
		foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {

			$value = wp_kses( $meta->display_value, [] );
			if(trim($meta->value) != 'e-bon-cadeau' && !(strpos($value,'Sans') !== false) ){

				// Contains Aquatonic
				if(strpos($value,'Aquatonic') !== false){
					$value .= ' '.__('(Come one hour before your treatments to enjoy)', 'tmsm-woocommerce-vouchers');
				}
				$strings[] = '<strong class="wc-item-meta-label">' . wp_kses( $meta->display_key, [] ) . ':</strong> ' . $value;
			}
		}
		if ( $strings ) {
			$item_meta .= $args['before'] . implode( $args['separator'], $strings ) . $args['after'];
		}



		$voucher_codes = explode(', ', $voucher_code);

		if( isset( $voucher_codes[($pdf_args['download_key'] - 1)] ) ) {
			$voucher_code = $voucher_codes[($pdf_args['download_key'] - 1)];
		}

		$voucher_expirydate	= wc_get_order_item_meta( $item_id, '_expirydate', true );


		if(!empty($voucher_code) && !empty($order) ){

			$recipient_title	= wc_get_order_item_meta( $item_id, '_recipienttitle', true );
			$recipient_firstname	= wc_get_order_item_meta( $item_id, '_recipientfirstname', true );
			$recipient_lastname	= wc_get_order_item_meta( $item_id, '_recipientlastname', true );
			$recipient_birthdate	= wc_get_order_item_meta( $item_id, '_recipientbirthdate', true );
			$recipient_address	= wc_get_order_item_meta( $item_id, '_recipientaddress', true );
			$recipient_zipcode	= wc_get_order_item_meta( $item_id, '_recipientzipcode', true );
			$recipient_city	= wc_get_order_item_meta( $item_id, '_recipientcity', true );
			$recipient_country	= wc_get_order_item_meta( $item_id, '_recipientcountry', true );
			$recipient_message	= wc_get_order_item_meta( $item_id, '_recipientmessage', true );

			$recipient = [];
			$recipient_name = '';
			if ( !empty($recipient_firstname) && !empty($recipient_lastname) ) {
				switch ( $recipient_title ) {
					case 1:
						$title = __( 'Mr', 'tmsm-woocommerce-vouchers' ) . ' ';
						break;
					case 2:
						$title = __( 'Ms', 'tmsm-woocommerce-vouchers' ) . ' ';
						break;
					default:
						$title = '';
						break;
				}

				$recipient = array(
					'first_name' => $title . $recipient_firstname,
					'last_name'  => strtoupper($recipient_lastname),
					//'address_1'  => $recipient_address,
					//'address_2'   => '',
					//'city'       => $recipient_city,
					//'postcode'   => $recipient_zipcode,
					//'state'    => '',
					//'country'    => $recipient_country,
				);

				$formatted_recipient = WC()->countries->get_formatted_address( $recipient );
				$recipient_name           =  '<div class="tmsmvoucher-pdf-recipient-name"><b>'.__( 'This voucher is reserved to:', 'tmsm-woocommerce-vouchers' ) .  ' '. $formatted_recipient.'</b></div>';
			}

			if(!empty($recipient_message)){
				$recipient_message = '<div class="tmsmvoucher-pdf-recipient-message"><b>'.__( 'Personal message:', 'tmsm-woocommerce-vouchers' ) .  '</b><br>'. $recipient_message.'</div>';
			}

			$localbusiness_logo = '';
			$localbusiness_id = $product->get_meta('_tmsm_woocommerce_vouchers_localbusiness');
			$localbusiness = get_post($localbusiness_id);
			$localbusiness_logo = strip_tags(get_the_post_thumbnail($localbusiness_id, 'full', ['data-no-lazy' => 1, 'class' => 'tmsmvoucher-pdf-localbusiness-logo']), '<img>');
			$localbusiness_info1 = null;
			$localbusiness_info2 = null;
			$localbusiness_intro = null;
			$localbusiness_booking = null;
			$localbusiness_color = null;
			$localbusiness_address = null;
			$localbusiness_name = null;

			if(function_exists('get_field')){
				$localbusiness_name = $localbusiness->post_title;
				$localbusiness_intro = get_field('voucher_intro', $localbusiness_id);
				$localbusiness_info1 = get_field('voucher_info1', $localbusiness_id);
				$localbusiness_info2 = get_field('voucher_info2', $localbusiness_id);
				$localbusiness_booking = get_field('voucher_booking', $localbusiness_id);
				$localbusiness_address = get_field('voucher_address', $localbusiness_id);
				$localbusiness_color = get_field('voucher_color', $localbusiness_id);
			}

			$html = '';
			$html .= '<div class="tmsmvoucher-pdf">';
			$html .= '
				<div class="tmsmvoucher-pdf-part tmsmvoucher-pdf-part-1 tmsmvoucher-pdf-part-dotted" >
					<div class="tmsmvoucher-pdf-voucher-graphic">
					{voucher_graphic}
					</div>
					<div class="tmsmvoucher-pdf-localbusiness-header">
					{localbusiness_logo}
					{localbusiness_intro}
					</div>
				</div>
				<div class="tmsmvoucher-pdf-part tmsmvoucher-pdf-part-2 tmsmvoucher-pdf-part-dotted"><div class="tmsmvoucher-pdf-product-control">{product_image}{voucher_code}{voucher_expirydate}{voucher_barcode}</div><div class="tmsmvoucher-pdf-product-data">{product_name}{product_breadcrumb}{product_description}{recipient_name}{recipient_message}</div></div>

				<div class="tmsmvoucher-pdf-part tmsmvoucher-pdf-part-3" style="">
				{localbusiness_booking}
				{localbusiness_info}
				{localbusiness_address}
				</div>
				';
			$html .= '</div>';

			if(!empty($localbusiness_intro)){
				$localbusiness_intro = '<div class="tmsmvoucher-pdf-localbusiness-intro">'.$localbusiness_intro.'</div>';
			}

			$product_image = strip_tags($product->get_image('shop_single', ['data-no-lazy'=> 1, 'class' => 'tmsmvoucher-pdf-product-image']), '<img>');
			$product_name = '<div class="tmsmvoucher-pdf-product-name" style="'.(!empty($localbusiness_color)?'background:'.$localbusiness_color:'').'">'.($product->get_meta('_alg_wc_product_open_pricing_enabled') === 'yes' ? $item->get_name() : $product->get_name() ).'</div>';
			$product_description = nl2br($product->get_meta('_tmsm_woocommerce_vouchers_description'));
			$product_description = apply_filters('tmsm_woocommerce_vouchers_description', $product_description, $product, $order );
			$product_description = $item_meta.'<div class="tmsmvoucher-pdf-product-description">'.$product_description.'</div>';




			// Breadcrumb only for subcategories of main Spa category
			$product_breadcrumb                     = '';
			$primary_category = get_post_meta( $product->get_id(), 'rank_math_primary_category', true );
			if (  ! empty( $primary_category ) ) {
				$first_category = $primary_category;
			}
			else{
				$product_categories                     = $product->get_category_ids();
				$first_category                         = array_pop( $product_categories );
			}
			$product_parent_categories_all_hierachy = get_ancestors( $first_category, 'product_cat' );
			//array_pop( $product_parent_categories_all_hierachy );
			$product_parent_categories_all_hierachy   = array_reverse( $product_parent_categories_all_hierachy );
			$product_parent_categories_all_hierachy[] = $first_category;
			$counter                                  = 0;
			$is_spa_sub_category = false;
			foreach ( $product_parent_categories_all_hierachy as $parent_cat_value ) {
				$term = get_term_by( "id", $parent_cat_value, 'product_cat' );
				if($term->term_id == get_option( 'tmsm_aquos_spa_booking_productcat' ) ){
					$is_spa_sub_category = true;
				}
				else{
					if ( $counter > 0 ) {
						$product_breadcrumb .= ' &gt;';
					}
					$product_breadcrumb .= ' ' . $term->name ;
					$counter ++;
				}

			}
			if ( ! empty( $product_breadcrumb )) {
				$product_breadcrumb = '<div class="tmsmvoucher-pdf-voucher-breadcrumb">' . __( 'Category:', 'tmsm-woocommerce-vouchers' ) . ' '
				                      . $product_breadcrumb . '</div><br>';
			}
			if($is_spa_sub_category == false){
				$product_breadcrumb = null;
			}

			if(!empty($voucher_expirydate)){
				$voucher_expirydate = '<div class="tmsmvoucher-pdf-voucher-expirydate"><b>'._x( 'Expires:', 'Voucher PDF', 'tmsm-woocommerce-vouchers' ) .  '</b> '. date_i18n( get_option( 'date_format' ), strtotime( $voucher_expirydate ) ).'</div>';
			}

			$voucher_barcode = '<div class="tmsmvoucher-pdf-barcode-container"><barcode class="tmsmvoucher-pdf-barcode" code="'.$voucher_code.'" type="C128A" height="1" text="2" size="0.94"/></div>';

			$voucher_code = '<div class="tmsmvoucher-pdf-voucher-code"><b>'.__( 'N°', 'tmsm-woocommerce-vouchers' ) .  '</b> '. $voucher_code.'</div>';

			$voucher_graphic = '<img src="'.plugin_dir_url( __FILE__ ) . 'img/voucher-graphic.png'.'" class="tmsmvoucher-pdf-voucher-graphic-image"/>';

			if(!empty($localbusiness_booking)){
				$localbusiness_booking = apply_filters('tmsm_woocommerce_vouchers_booking', $localbusiness_booking, $product, $order );
				$localbusiness_booking = '<div class="tmsmvoucher-pdf-localbusiness-booking"><div class="tmsmvoucher-pdf-localbusiness-booking-data">'.$localbusiness_booking.'</div></div>';

			}

			if(!empty($localbusiness_info1)){
				$localbusiness_info1 = apply_filters('tmsm_woocommerce_vouchers_info1', $localbusiness_info1, $product, $order );
				$localbusiness_info1 = '<div class="tmsmvoucher-pdf-localbusiness-info1">'.$localbusiness_info1.'</div>';
			}
			if(!empty($localbusiness_info2)){
				$localbusiness_info2 = apply_filters('tmsm_woocommerce_vouchers_info2', $localbusiness_info2, $product, $order );
				$localbusiness_info2 = '<div class="tmsmvoucher-pdf-localbusiness-info2">'.$localbusiness_info2.'</div>';
			}

			$localbusiness_info = $localbusiness_info1 . $localbusiness_info2;
			if(!empty($localbusiness_info)) {
				$localbusiness_info = '<div class="tmsmvoucher-pdf-localbusiness-info">' . $localbusiness_info . '</div>';
			}

			$items_tags = [
				'{localbusiness_booking}',
				'{localbusiness_info}',
				'{localbusiness_name}',
				'{localbusiness_address}',
				'{localbusiness_logo}',
				'{localbusiness_intro}',
				'{voucher_graphic}',
				'{voucher_code}',
				'{voucher_expirydate}',
				'{voucher_barcode}',
				'{product_image}',
				'{product_name}',
				'{product_breadcrumb}',
				'{product_description}',
				'{recipient_name}',
				'{recipient_message}',
			];

			$items_values = [
				$localbusiness_booking,
				$localbusiness_info,
				$localbusiness_name,
				$localbusiness_address,
				$localbusiness_logo,
				$localbusiness_intro,
				$voucher_graphic,
				$voucher_code,
				$voucher_expirydate,
				$voucher_barcode,
				$product_image,
				$product_name,
				$product_breadcrumb,
				$product_description,
				$recipient_name,
				$recipient_message,
			];

			$html = str_replace($items_tags, $items_values, $html);

			//$this->tmsmvoucher_output_mpdf6_from_html( $html, $pdf_args );

			$this->tmsmvoucher_output_mpdf8_from_html( $html, $pdf_args );
		}
	}

	/**
	 * Output a pdf from html content (with MPDF 6.1.4)
	 *
	 * @param string $html
	 * @param array $pdf_args
	 * @throws MpdfException
	 */
	private function tmsmvoucher_output_mpdf6_from_html($html, $pdf_args){
		$pdf_save = !empty($pdf_args['pdf_save']) ? 'F' : 'I'; // Pdf store in a folder or not
		$pdf_filepath = !empty($pdf_args['pdf_filepath']) ? $pdf_args['pdf_filepath'] : '';
		if (!class_exists('mPDF')) { //If class not exist
			require_once TMSMWOOCOMMERCEVOUCHERS_PLUGINDIR . 'includes/mpdf/mpdf.php';
		}
		$mpdf = new mPDF('UTF-8', 'A4',  12,  'dejavusans', 0, 0, 0, 0, 0, 0, $orientation = 'P');
		$stylesheet = file_get_contents(plugin_dir_url( __FILE__ ) . 'css/tmsm-woocommerce-vouchers-public.css');
		$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->dpi = 96;
		$mpdf->img_dpi = 96;
		$mpdf->WriteHTML($html, 2);
		$mpdf->autoPageBreak = true;
		$mpdf->setAutoTopMargin = 'pad';
		$mpdf->setAutoBottomMargin = 'pad';
		$mpdf->bleedMargin = 0;
		$mpdf->margBuffer = 0;
		$mpdf->nonPrintMargin = 0;
		$mpdf->SetDisplayMode('fullpage', 'single');
		$mpdf->debug = true;
		$mpdf->showImageErrors = true;
		$mpdf->Output($pdf_filepath, $pdf_save);
	}

	/**
	 * Output a pdf from html content (with MPDF 8.0.2)
	 *
	 * @param $html
	 * @param $pdf_args
	 *
	 * @throws MpdfException
	 * @throws \Mpdf\MpdfException
	 */
	private function tmsmvoucher_output_mpdf8_from_html($html, $pdf_args){

		$pdf_save = !empty($pdf_args['pdf_save']) ? 'F' : 'I'; // Pdf store in a folder or not
		$pdf_filepath = !empty($pdf_args['pdf_filepath']) ? $pdf_args['pdf_filepath'] : '';

		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'UTF-8',
			'format' => 'A4',
			'default_font_size' => 12,
			'default_font'=> 'dejavusans',
			'mgl' => 0,
			'mgr' => 0,
			'mgt' => 0,
			'mgb' => 0,
			'mgh' => 0,
			'mgf' => 0,
			'orientation' => 'P',
			//'tempDir' => get_temp_dir().'/mpdf/',
			'bleedMargin' => 0,
			'crossMarkMargin' => 0,
			'cropMarkMargin' => 0,
			'cropMarkLength' => 0,
			'nonPrintMargin' => 0,
			'margin_header' => 0,
			'margin_footer' => 0,
			'margin_right' => 0,
			'margin_left' => 0,
			'margin_top' => 0,
			'margin_bottom' => 0
		]);
		// TODO décommenter pour la prod
		// $stylesheet = file_get_contents(plugin_dir_url( __FILE__ ) . 'css/tmsm-woocommerce-vouchers-public.css');
		$stylesheet = file_get_contents(plugin_dir_path( __FILE__ ) . 'css/tmsm-woocommerce-vouchers-public.css');

		$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->dpi = 96;
		$mpdf->img_dpi = 96;
		$mpdf->WriteHTML($html, 2);
		$mpdf->autoPageBreak = true;
		$mpdf->setAutoTopMargin = 'pad';
		$mpdf->setAutoBottomMargin = 'pad';
		$mpdf->bleedMargin = 0;
		$mpdf->margBuffer = 0;
		$mpdf->nonPrintMargin = 0;
		$mpdf->SetDisplayMode('fullpage', 'single');
		$mpdf->debug = false;
		$mpdf->SetMargins(0, 0, 0);
		$mpdf->showImageErrors = true;

		$mpdf->Output($pdf_filepath, $pdf_save);

	}

	/**
	 * Fix download link in WooCommerce 6.5
	 *
	 * @param $downloads
	 * @param $obj
	 *
	 * @since 1.3.8
	 *
	 * @return array
	 */
	public function set_dummy_product_download( $downloads, $obj ): array {

		if ( isset( $_GET['download_file'], $_GET['order'], $_GET['key'] ) && ( isset( $_GET['email'] ) || isset( $_GET['uid'] ) ) ) {
			$key       = $_GET['key'];
			$check_key = strpos( $key, 'tmsmvoucher' );

			if ( $check_key !== false ) {

				$product_id = absint( $_GET['download_file'] );
				$product    = wc_get_product( $product_id );
				$key        = empty( $_GET['key'] ) ? '' : sanitize_text_field( wp_unslash( $_GET['key'] ) );

				$pd_object = new WC_Product_Download();

				$pd_object->set_id( $key );
				$downloads[ $key ] = $pd_object;
			}
		}

		return $downloads;
	}

}