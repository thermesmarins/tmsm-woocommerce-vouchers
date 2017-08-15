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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-woocommerce-vouchers-public.css', array(), $this->version,
			'all' );

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-woocommerce-vouchers-public.js', array( 'jquery' ),
			$this->version, true );

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
			$is_voucher = false;
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

			$is_voucher      = get_post_meta( $variation_id, '_voucher', true ) == 'yes';
			$is_virtual      = get_post_meta( $variation_id, '_virtual', true ) == 'yes';
			$is_downloadable = get_post_meta( $variation_id, '_downloadable', true ) == 'yes';


			$settings_physical           = get_option( 'tmsm_woocommerce_vouchers_physical' ) == 'yes';
			$settings_virtual            = get_option( 'tmsm_woocommerce_vouchers_virtual' ) == 'yes';
			$settings_recipientoptionnal = get_option( 'tmsm_woocommerce_vouchers_recipientoptionnal' ) == 'yes';
			/*
			echo ' - $settings_physical:'.$settings_physical;
			echo ' - $settings_virtual:'.$settings_virtual;
			*/

			if ( $is_virtual && ! $settings_virtual ) {
				$is_voucher = false;
			}
			if ( ! $is_virtual && ! $settings_physical ) {
				$is_voucher = false;
			}

			/*
						echo ' - $product_id:'.$product_id;
						echo ' - $variation_id:'.$variation_id;
						echo ' - $is_voucher:'.$is_voucher;
			*/
			if ( $is_voucher ) { // if voucher is enable


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
					? wp_filter_nohtml_kses( $_POST['_recipientfirstname'][ $variation_id ] ) : '';

				$submit_recipientlastname = isset( $_POST['_recipientlastname'][ $variation_id ] )
					? wp_filter_nohtml_kses( $_POST['_recipientlastname'][ $variation_id ] ) : '';

				$submit_recipienttitle = isset( $_POST['_recipienttitle'][ $variation_id ] )
					? wp_filter_nohtml_kses( $_POST['_recipienttitle'][ $variation_id ] ) : '';

				$submit_recipientbirthdate = isset( $_POST['_recipientbirthdate'][ $variation_id ] )
					? wp_filter_nohtml_kses( $_POST['_recipientbirthdate'][ $variation_id ] ) : '';

				$submit_recipientaddress = isset( $_POST['_recipientaddress'][ $variation_id ] )
					? wp_filter_nohtml_kses( $_POST['_recipientaddress'][ $variation_id ] ) : '';

				$submit_recipientzipcode = isset( $_POST['_recipientzipcode'][ $variation_id ] )
					? wp_filter_nohtml_kses( $_POST['_recipientzipcode'][ $variation_id ] ) : '';

				$submit_recipientcity = isset( $_POST['_recipientcity'][ $variation_id ] )
					? wp_filter_nohtml_kses( $_POST['_recipientcity'][ $variation_id ] ) : '';

				$submit_recipientcountry = isset( $_POST['_recipientcountry'][ $variation_id ] )
					? wp_filter_nohtml_kses( $_POST['_recipientcountry'][ $variation_id ] ) : '';

				$submit_recipientmobilephone = isset( $_POST['_recipientmobilephone'][ $variation_id ] )
					? wp_filter_nohtml_kses( $_POST['_recipientmobilephone'][ $variation_id ] ) : '';

				$submit_recipientemail = isset( $_POST['_recipientemail'][ $variation_id ] )
					? wp_filter_nohtml_kses( $_POST['_recipientemail'][ $variation_id ] ) : '';

				$submit_recipientmessage = isset( $_POST['_recipientmessage'][ $variation_id ] )
					? wp_filter_nohtml_kses( $_POST['_recipientmessage'][ $variation_id ] ) : '';

				$submit_recipientsenddate = isset( $_POST['_recipientsenddate'][ $variation_id ] )
					? wp_filter_nohtml_kses( $_POST['_recipientsenddate'][ $variation_id ] ) : '';


				echo '<div class="vouchers-fields-wrapper' . ( $product->is_type( 'variation' ) ? '-variation' : '' )
				     . '" id="vouchers-fields-wrapper-' . $variation_id . '" >';
				echo '<p class="h4 vouchers-fields-title">';
				echo __( 'Recipient of the voucher', 'tmsm-woocommerce-vouchers' );
				echo '</p>';
				echo '<div class="vouchers-fields">';


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
						'type'         => 'select',
						'label'        => __( 'Recipient title:', 'tmsm-woocommerce-vouchers' ),
						'description'  => '',
						'required'     => $settings_recipienttitlerequired,
						'autocomplete' => 'honorific-prefix',
						'id'           => '_recipienttitle[' . $variation_id . ']',
						//'placeholder' => __( 'Pick a title', 'tmsm-woocommerce-vouchers' ),
						'options'      => [
							'' => '',
							1  => __( 'Ms', 'tmsm-woocommerce-vouchers' ),
							2  => __( 'Mr', 'tmsm-woocommerce-vouchers' ),
						],
						'class'        => [
							'form-row-wide',
							'title-field',
							'formfield-text',
							'form-group',
							( $settings_recipienttitlerequired && isset( $_POST['_recipienttitle'][ $variation_id ] )
							  && empty( $submit_recipienttitle ) ? 'has-error' : '' ),
						],
					], $submit_recipienttitle );

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
					], $submit_recipientfirstname );
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
					], $submit_recipientlastname );
				endif;

				// address
				if ( $settings_recipientaddress ):
					woocommerce_form_field( '_recipientaddress[' . $variation_id . ']', [
						'type'         => 'text',
						'label'        => __( 'Recipient address:', 'tmsm-woocommerce-vouchers' ),
						'description'  => '',
						'required'     => $settings_recipientaddressrequired,
						'autocomplete' => 'street-address',
						'id'           => '_recipientaddress[' . $variation_id . ']',
						'class'        => [
							'form-row-wide',
							'address-field',
							'formfield-text',
							'form-group',
							( $settings_recipientaddressrequired && isset( $_POST['_recipientaddress'][ $variation_id ] )
							  && empty( $submit_recipientaddress ) ? 'has-error' : '' ),
						],
					], $submit_recipientaddress );
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
					], $submit_recipientzipcode );
				endif;

				// city
				if ( $settings_recipientcity ):
					woocommerce_form_field( '_recipientcity[' . $variation_id . ']', [
						'type'         => 'text',
						'label'        => __( 'Recipient city:', 'tmsm-woocommerce-vouchers' ),
						'description'  => '',
						'required'     => $settings_recipientcityrequired,
						'autocomplete' => 'locality',
						'id'           => '_recipientcity[' . $variation_id . ']',
						'class'        => [
							'form-row-wide',
							'address-field',
							'formfield-text',
							'form-group',
							( $settings_recipientcityrequired && isset( $_POST['_recipientcity'][ $variation_id ] ) && empty( $submit_recipientcity )
								? 'has-error' : '' ),
						],
					], $submit_recipientcity );
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
					], $submit_recipientcountry );

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
					], $submit_recipientmobilephone );
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
					], $submit_recipientemail );
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

		$is_voucher = get_post_meta( $variation_id, '_voucher', true ) == 'yes';

		if ( $is_voucher ) {

			$settings_recipientoptionnal = get_option( 'tmsm_woocommerce_vouchers_recipientoptionnal' ) == 'yes';

			// title
			$settings_recipienttitle         = get_option( 'tmsm_woocommerce_vouchers_recipienttitle' ) == 'yes';
			$settings_recipienttitlerequired = get_option( 'tmsm_woocommerce_vouchers_recipienttitlerequired' ) == 'yes';
			$submit_recipienttitle           = isset( $_POST['_recipienttitle'][ $variation_id ] )
				? wp_filter_nohtml_kses( $_POST['_recipienttitle'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipienttitlerequired && empty( $submit_recipienttitle ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient title', 'tmsm-woocommerce-vouchers' ) . ' ' . __( 'is required.',
						'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// firstname
			$settings_recipientfirstname         = get_option( 'tmsm_woocommerce_vouchers_recipientfirstname' ) == 'yes';
			$settings_recipientfirstnamerequired = get_option( 'tmsm_woocommerce_vouchers_recipientfirstnamerequired' ) == 'yes';
			$submit_recipientfirstname           = isset( $_POST['_recipientfirstname'][ $variation_id ] )
				? wp_filter_nohtml_kses( $_POST['_recipientfirstname'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientfirstnamerequired && empty( $submit_recipientfirstname ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient first name', 'tmsm-woocommerce-vouchers' ) . ' '
				               . __( 'is required.', 'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// lastname
			$settings_recipientlastname         = get_option( 'tmsm_woocommerce_vouchers_recipientlastname' ) == 'yes';
			$settings_recipientlastnamerequired = get_option( 'tmsm_woocommerce_vouchers_recipientlastnamerequired' ) == 'yes';
			$submit_recipientlastname           = isset( $_POST['_recipientlastname'][ $variation_id ] )
				? wp_filter_nohtml_kses( $_POST['_recipientlastname'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientlastnamerequired && empty( $submit_recipientlastname ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient last name', 'tmsm-woocommerce-vouchers' ) . ' '
				               . __( 'is required.', 'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// address
			$settings_recipientaddress         = get_option( 'tmsm_woocommerce_vouchers_recipientaddress' ) == 'yes';
			$settings_recipientaddressrequired = get_option( 'tmsm_woocommerce_vouchers_recipientaddressrequired' ) == 'yes';
			$submit_recipientaddress           = isset( $_POST['_recipientaddress'][ $variation_id ] )
				? wp_filter_nohtml_kses( $_POST['_recipientaddress'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientaddressrequired && empty( $submit_recipientaddress ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient address', 'tmsm-woocommerce-vouchers' ) . ' '
				               . __( 'is required.', 'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// zipcode
			$settings_recipientzipcode         = get_option( 'tmsm_woocommerce_vouchers_recipientzipcode' ) == 'yes';
			$settings_recipientzipcoderequired = get_option( 'tmsm_woocommerce_vouchers_recipientzipcoderequired' ) == 'yes';
			$submit_recipientzipcode           = isset( $_POST['_recipientzipcode'][ $variation_id ] )
				? wp_filter_nohtml_kses( $_POST['_recipientzipcode'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientzipcoderequired && empty( $submit_recipientzipcode ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient zipcode', 'tmsm-woocommerce-vouchers' ) . ' '
				               . __( 'is required.', 'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// city
			$settings_recipientcity         = get_option( 'tmsm_woocommerce_vouchers_recipientcity' ) == 'yes';
			$settings_recipientcityrequired = get_option( 'tmsm_woocommerce_vouchers_recipientcityrequired' ) == 'yes';
			$submit_recipientcity           = isset( $_POST['_recipientcity'][ $variation_id ] )
				? wp_filter_nohtml_kses( $_POST['_recipientcity'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientcityrequired && empty( $submit_recipientcity ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient city', 'tmsm-woocommerce-vouchers' ) . ' ' . __( 'is required.',
						'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// country
			$settings_recipientcountry         = get_option( 'tmsm_woocommerce_vouchers_recipientcountry' ) == 'yes';
			$settings_recipientcountryrequired = get_option( 'tmsm_woocommerce_vouchers_recipientcountryrequired' ) == 'yes';
			$submit_recipientcountry           = isset( $_POST['_recipientcountry'][ $variation_id ] )
				? wp_filter_nohtml_kses( $_POST['_recipientcountry'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientcountryrequired && empty( $submit_recipientcountry ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient country', 'tmsm-woocommerce-vouchers' ) . ' '
				               . __( 'is required.', 'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// mobilephone
			$settings_recipientmobilephone         = get_option( 'tmsm_woocommerce_vouchers_recipientmobilephone' ) == 'yes';
			$settings_recipientmobilephonerequired = get_option( 'tmsm_woocommerce_vouchers_recipientmobilephonerequired' ) == 'yes';
			$submit_recipientmobilephone           = isset( $_POST['_recipientmobilephone'][ $variation_id ] )
				? wp_filter_nohtml_kses( $_POST['_recipientmobilephone'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientmobilephonerequired && empty( $submit_recipientmobilephone ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient phone', 'tmsm-woocommerce-vouchers' ) . ' ' . __( 'is required.',
						'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			}

			// email
			$settings_recipientemail         = get_option( 'tmsm_woocommerce_vouchers_recipientemail' ) == 'yes';
			$settings_recipientemailrequired = get_option( 'tmsm_woocommerce_vouchers_recipientemailrequired' ) == 'yes';
			$submit_recipientemail           = isset( $_POST['_recipientemail'][ $variation_id ] )
				? wp_filter_nohtml_kses( $_POST['_recipientemail'][ $variation_id ] ) : '';
			if ( ! $settings_recipientoptionnal && $settings_recipientemailrequired && empty( $submit_recipientemail ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Recipient email', 'tmsm-woocommerce-vouchers' ) . ' ' . __( 'is required.',
						'tmsm-woocommerce-vouchers' ) . '</p>', 'error' );
				$valid = false;
			} elseif ( ! empty( $submit_recipientemail ) && ! is_email( $submit_recipientemail ) ) {
				wc_add_notice( '<p class="vouchers-fields-error">' . __( 'Please enter valid email', 'woovoucher' ) . '</p>', 'error' );
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
			? wp_filter_nohtml_kses( $_POST['_recipienttitle'][ $variation_id ] ) : '';
		$cart_item_data['_recipienttitle'] = $submit_recipienttitle;

		// firstname
		$submit_recipientfirstname             = isset( $_POST['_recipientfirstname'][ $variation_id ] )
			? wp_filter_nohtml_kses( $_POST['_recipientfirstname'][ $variation_id ] ) : '';
		$cart_item_data['_recipientfirstname'] = $submit_recipientfirstname;

		// lastname
		$submit_recipientlastname             = isset( $_POST['_recipientlastname'][ $variation_id ] )
			? wp_filter_nohtml_kses( $_POST['_recipientlastname'][ $variation_id ] ) : '';
		$cart_item_data['_recipientlastname'] = $submit_recipientlastname;

		// address
		$submit_recipientaddress             = isset( $_POST['_recipientaddress'][ $variation_id ] )
			? wp_filter_nohtml_kses( $_POST['_recipientaddress'][ $variation_id ] ) : '';
		$cart_item_data['_recipientaddress'] = $submit_recipientaddress;

		// zipcode
		$submit_recipientzipcode             = isset( $_POST['_recipientzipcode'][ $variation_id ] )
			? wp_filter_nohtml_kses( $_POST['_recipientzipcode'][ $variation_id ] ) : '';
		$cart_item_data['_recipientzipcode'] = $submit_recipientzipcode;

		// city
		$submit_recipientcity             = isset( $_POST['_recipientcity'][ $variation_id ] )
			? wp_filter_nohtml_kses( $_POST['_recipientcity'][ $variation_id ] ) : '';
		$cart_item_data['_recipientcity'] = $submit_recipientcity;

		// country
		$submit_recipientcountry             = isset( $_POST['_recipientcountry'][ $variation_id ] )
			? wp_filter_nohtml_kses( $_POST['_recipientcountry'][ $variation_id ] ) : '';
		$cart_item_data['_recipientcountry'] = $submit_recipientcountry;

		// mobilephone
		$submit_recipientmobilephone             = isset( $_POST['_recipientmobilephone'][ $variation_id ] )
			? wp_filter_nohtml_kses( $_POST['_recipientmobilephone'][ $variation_id ] ) : '';
		$cart_item_data['_recipientmobilephone'] = $submit_recipientmobilephone;

		// email
		$submit_recipientemail             = isset( $_POST['_recipientemail'][ $variation_id ] )
			? wp_filter_nohtml_kses( $_POST['_recipientemail'][ $variation_id ] ) : '';
		$cart_item_data['_recipientemail'] = $submit_recipientemail;


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

		if ( $item['_recipientlastname'] && $item['_recipientfirstname'] ) {
			switch ( $item['_recipienttitle'] ) {
				case 1:
					$title = __( 'Ms', 'tmsm-woocommerce-vouchers' ) . ' ';
					break;
				case 2:
					$title = __( 'Mr', 'tmsm-woocommerce-vouchers' ) . ' ';
					break;
				default:
					$title = '';
					break;

			}
			$recipient = array(
				'first_name' => $title . $item['_recipientfirstname'],
				'last_name'  => $item['_recipientlastname'],
				'address_1'  => $item['_recipientaddress'],
				//'address_2'   => '',
				'city'       => $item['_recipientcity'],
				'postcode'   => $item['_recipientzipcode'],
				//'state'    => '',
				'country'    => $item['_recipientcountry'],
			);

			$formatted_recipient = WC()->countries->get_formatted_address( $recipient );
			$data[]              = array(
				'name'    => __( 'Recipient', 'tmsm-woocommerce-vouchers' ),
				'display' => $formatted_recipient,
				'hidden'  => false,
				'value'   => ''
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
	public function woocommerce_display_item_meta( $html, $item, $args ) {

		$strings = [];

		if ( $item['_recipientlastname'] && $item['_recipientfirstname'] ) {
			switch ( $item['_recipienttitle'] ) {
				case 1:
					$title = __( 'Ms', 'tmsm-woocommerce-vouchers' ) . ' ';
					break;
				case 2:
					$title = __( 'Mr', 'tmsm-woocommerce-vouchers' ) . ' ';
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

		if ( $strings != [] ) {
			$html = $args['before'] . implode( $args['separator'], $strings ) . $args['after'];
		}

		return $html;
	}


	/**
	 * Generate unique code
	 *
	 * @since 1.0.0
	 *
	 * @param integer $product_id
	 * @param integer $variation_id
	 *
	 * @return string
	 */
	public function tmsmvoucher_generate_code( $product_id, $variation_id ) {

		error_log('*** tmsmvoucher_generate_code');

		$code = '555555'; //@TODO function to generate unique codes

		return $code;
	}

	/**
	 * Adds voucher data to order
	 *
	 * @since 1.0.0
	 *
	 * @param $order_id
	 *
	 * @return void
	 */
	public function woocommerce_checkout_update_order_meta($order_id){

		error_log('*** woocommerce_checkout_update_order_meta');

		$order = wc_get_order($order_id);
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
				$codes_item_meta = wc_get_order_item_meta($item_id, '_vouchercode');

				if (empty($codes_item_meta)) {// If voucher data are not empty so code get executed once only

					$is_voucher      = get_post_meta( $data_id, '_voucher', true ) == 'yes';
					$is_virtual      = get_post_meta( $data_id, '_virtual', true ) == 'yes';
					$is_downloadable = get_post_meta( $data_id, '_downloadable', true ) == 'yes';

					$settings_physical           = get_option( 'tmsm_woocommerce_vouchers_physical' ) == 'yes';
					$settings_virtual            = get_option( 'tmsm_woocommerce_vouchers_virtual' ) == 'yes';
					$settings_recipientoptionnal = get_option( 'tmsm_woocommerce_vouchers_recipientoptionnal' ) == 'yes';

					if ( $is_virtual && ! $settings_virtual ) {
						$is_voucher = false;
					}
					if ( ! $is_virtual && ! $settings_physical ) {
						$is_voucher = false;
					}

					if ( $is_voucher ) { // if voucher is enable

						$code = $this->tmsmvoucher_generate_code($product_id, $variation_id);

						$start_date = date('Y-m-d H:i:s', time()); // format start date

						//get days difference
						$days_diff = 365;
						$add_days = '+' . $days_diff . ' days';
						$exp_date = date('Y-m-d H:i:s', strtotime($order_date . $add_days));

						//add voucher codes item meta "Now we store voucher codes in item meta fields"
						wc_add_order_item_meta($item_id, '_vouchercode', $code);

					}


				}
			}


		}

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

		$order = wc_get_order( $order_id );

		error_log('*** woocommerce_grant_product_download_permissions');

		$downloadable_files = [];

		if ( sizeof( $order->get_items() ) > 0 ) { //Get all items in order
			foreach ( $order->get_items() as $item_id => $item ) {

				$product = $item->get_product();
				//$_product = $order->get_product_from_item($item); //Get product from Item ( It is required otherwise multipdf voucher link not work and global $woo_vou_item_id will not work )
				$variation_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : ''; // Taking variation id

				if ( $product && $product->exists() ) { // && $_product->is_downloadable()

					$product_id = $product->get_id();
					$data_id    = ( ! empty( $variation_id ) ) ? $variation_id : $product_id;

					$is_voucher      = get_post_meta( $data_id, '_voucher', true ) == 'yes';
					$is_virtual      = get_post_meta( $data_id, '_virtual', true ) == 'yes';
					$is_downloadable = get_post_meta( $data_id, '_downloadable', true ) == 'yes';

					$settings_physical = get_option( 'tmsm_woocommerce_vouchers_physical' ) == 'yes';
					$settings_virtual  = get_option( 'tmsm_woocommerce_vouchers_virtual' ) == 'yes';

					if ( $is_virtual && ! $settings_virtual ) {
						$is_voucher = false;
					}
					if ( ! $is_virtual && ! $settings_physical ) {
						$is_voucher = false;
					}

					if ( $is_voucher ) {

						$downloadable_files = $this->tmsmvoucher_download_key( $order_id, $data_id, $item_id );

						foreach ( array_keys( $downloadable_files ) as $download_id ) {
							error_log('wc_downloadable_file_permission '.$download_id);
							wc_downloadable_file_permission( $download_id, $data_id, $order );
						}
					}

				}
			}
		}

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
	public function tmsmvoucher_download_key( $order_id = '', $product_id = '', $item_id = '' ) {

		error_log('*** tmsmvoucher_download_key');

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
	public function tmsmvoucher_get_multi_voucher_key( $order_id = '', $product_id = '', $item_id = '' ) {

		error_log('*** tmsmvoucher_get_multi_voucher_key');

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
	 */
	public function tmsmvoucher_get_multi_voucher( $order_id = '', $product_id = '', $item_id = '' ) {

		error_log('*** tmsmvoucher_get_multi_voucher');

		$code	= wc_get_order_item_meta( $item_id, '_vouchercode', true );
		$vouchers = [];
		$vouchers['tmsmvoucher_pdf_'.$item_id]	= $code;
		return $vouchers;
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

		$product = $item->get_product();

		if ( ! ( $product && $order && $product->is_downloadable() && $order->is_download_permitted() ) ) {
			return $files;
		}

		$variation_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];

		$pdf_downloadable_files = $this->tmsmvoucher_download_key( $order->get_id(), $variation_id, $item->get_id() );

		if ( ! empty( $pdf_downloadable_files ) ) {

			foreach ( $pdf_downloadable_files as $pdf_key => $pdf_file_array ) {

				// Add download url to voucher downlodable files
				$pdf_downloadable_files[ $pdf_key ]['download_url'] = $item->get_item_download_url( $pdf_key );

				// Merge downlodable file to files
				$files = array_merge( $files, array( $pdf_key => $pdf_downloadable_files[ $pdf_key ] ) );
			}
		}

		// Add item id in download pdf url
		if ( ! empty( $files ) ) { //If files not empty
			foreach ( $files as $file_key => $file_data ) {

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
	 */
	public function woocommerce_customer_get_downloadable_products( $downloads = [] ) {

		return $downloads;

		error_log( 'bbb' );
		if ( is_user_logged_in() ) {//If user is logged in
			echo 'ddd';
			//Get user ID
			$user_id = get_current_user_id();

			//Get User Order Arguments
			$args = array(
				'numberposts' => - 1,
				'meta_key'    => '_customer_user',
				'meta_value'  => $user_id,
				'post_type'   => 'shop_order',
				'post_status' => array( 'wc-completed' ),
			);

			//user orders
			$user_orders = get_posts( $args );

			if ( ! empty( $user_orders ) ) {//If orders are not empty
				foreach ( $user_orders as $user_order ) {
					//Get order ID
					$order_id = isset( $user_order->ID ) ? $user_order->ID : '';

					if ( ! empty( $order_id ) ) {//Order it not empty
						global $vou_order;

						//Set global order ID
						$vou_order = $order_id;

						//Get cart details
						$order = wc_get_order( $order_id );
						$order_items  = $order->get_items();
						$order_date   = $order->get_date_modified(); // Get order date
						//$order_date   = date( 'F j, Y', strtotime( $order_date ) );

						if ( ! empty( $order_items ) ) {// Check cart details are not empty
							foreach ( $order_items as $item_id => $product_data ) {

								error_log( 'ccc' );
								$_product = $order->get_product_from_item( $product_data );

								if ( ! $_product ) {//If product deleted
									$download_file_data = array();
								} else {
									$download_file_data = $woo_vou_model->woo_vou_get_item_downloads_from_order( $order, $product_data );
								}

								//Get voucher codes
								$codes = wc_get_order_item_meta( $item_id, '_codes' );

								if ( ! empty( $download_file_data ) && ! empty( $codes ) ) {//If download exist and code is not empty
									foreach ( $download_file_data as $key => $download_file ) {

										//check download key is voucher key or not
										$check_key = strpos( $key, 'tmsmvoucher_pdf_' );

										//get voucher number
										$voucher_number = str_replace( 'tmsmvoucher_pdf_', '', $key );

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
											//get product name
											$product_name = $_product->get_title();
											error_log( 'ddd' );
											//Download file arguments
											$download_args = array(
												'product_id'          => $product_id,
												'product_name'        => $product_name,
												'download_url'        => $download_url,
												'download_name'       => $product_name . ' - ' . $download_file['name'] . ' ' . $voucher_number
												                         . ' ( ' . $order_date . ' )',
												'downloads_remaining' => '',
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

						//reset global order ID
						$vou_order = 0;
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
	 *
	 */
	function woocommerce_download_product( $email, $order_key, $product_id, $user_id, $download_id, $order_id ) {

		error_log( '*** woocommerce_download_product' );
		error_log( '$email: ' . $email );
		error_log( '$order_key: ' . $order_key );
		error_log( '$product_id: ' . $product_id );
		error_log( '$user_id: ' . $user_id );
		error_log( '$download_id: ' . $download_id );
		error_log( '$order_id: ' . $order_id );

		$item_id = wc_clean( $_GET['item_id'] );

		if ( ! empty( $item_id ) ) {

			$this->tmsmvoucher_generate_downloadfile($email, $product_id, $download_id, $order_id, $item_id);
		}
		exit;
	}

	/**
	 * Check item is already exist in order
	 *
	 * Handles to check the item is already exist in order or not
	 *
	 * @since 1.0.0
	 *
	 * @param string $email
	 * @param string $product_id
	 * @param string $download_id
	 * @param string $order_id
	 * @param string $item_id
	 *
	 * @return void
	 */
	public function tmsmvoucher_generate_downloadfile( $email = '', $product_id = '', $download_id = '', $order_id = '', $item_id = '' ) {

		//$order_codes = $this->tmsm_get_order_codes( $order_id, $product_id, $item_id );

		error_log('*** tmsmvoucher_generate_downloadfile');

		$order_codes = [];
		$this->tmsmvoucher_voucher_html_template( $product_id, $order_id, $item_id, $order_codes );
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
	 * @param array  $order_codes
	 * @param array  $pdf_args
	 *
	 * @return void
	 */
	function tmsmvoucher_voucher_html_template( $product_id, $order_id, $item_id = '', $order_codes = [], $pdf_args = [] ) {
		global $current_user;

		error_log('*** tmsmvoucher_voucher_html_template');

		$voucher_template_html = '<b>Hello</b> World';
		$this->tmsmvoucher_output_pdf_by_html( $voucher_template_html, $pdf_args );
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
	public function tmsmvoucher_get_variation_data($order = array(), $item_key = '') {

		error_log('*** tmsmvoucher_get_variation_data');

		$items = $order->get_items();

		$item_array = $this->tmsmvoucher_get_item_data_using_item_key($items, $item_key);

		$item = isset($item_array['item_data']) ? $item_array['item_data'] : array();
		$item_id = isset($item_array['item_id']) ? $item_array['item_id'] : array();

		//Get product from Item ( It is required otherwise multipdf voucher link not work and global $woo_vou_item_id will not work )
		$_product = $order->get_product_from_item($item);

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
	public function tmsmvoucher_get_item_data_using_item_key($order_items, $item_key) {

		error_log('*** tmsmvoucher_get_item_data_using_item_key');

		//initilize item
		$return_item = array('item_id' => '', 'item_data' => array());

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
	public function tmsmvoucher_display_product_item_name($item = array(), $product = array()) {

		error_log('*** tmsmvoucher_display_product_item_name');

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
	public function tmsmvoucher_get_item_productid_from_product($product){

		if ( $product->is_type( 'variable' ) || $product->is_type( 'variation' ) ) {
			$product_id = $product->get_parent_id();
		} else {
			$product_id = $product->get_id();
		}

		return $product_id;
	}


}