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
 * @author     Nicolas Mollet <nmollet@thalassotherapie.com>
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-woocommerce-vouchers-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-woocommerce-vouchers-admin.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * Add your custom bulk action in dropdown
	 *
	 * @param $bulk_actions
	 *
	 * @return mixed
	 */
	function bulk_actions_processed( $bulk_actions ) {

		$bulk_actions['mark_processed'] = __('Mark as processed', 'tmsm-woocommerce-vouchers');

		return $bulk_actions;
	}


	/**
	 * Bulk action handler
	 */
	function admin_action_mark_processed() {

		// if an array with order IDs is not presented, exit the function
		if( !isset( $_REQUEST['post'] ) && !is_array( $_REQUEST['post'] ) )
			return;

		foreach( $_REQUEST['post'] as $order_id ) {
			$order = new WC_Order( $order_id );
			$order_note = __('Status changed to Processed', 'tmsm-woocommerce-vouchers');
			$order->update_status( 'processed', $order_note, true );
		}

		// of course using add_query_arg() is not required, you can build your URL inline
		$location = add_query_arg( array(
			'post_type' => 'shop_order',
			'marked_processed' => 1, // marked_processed=1 is just the $_GET variable for notices
			'changed' => count( $_REQUEST['post'] ), // number of changed orders
			'ids' => join( $_REQUEST['post'], ',' ),
			'post_status' => 'all'
		), 'edit.php' );

		wp_redirect( admin_url( $location ) );
		exit;

	}

	/**
	 * Action when order goes from processing to processed
	 *
	 * @param $order_id int
	 * @param $order WC_Order
	 */
	function status_processing_to_processed($order_id, $order){
		$order->update_status( 'completed');
		$order->update_status( 'processed');
	}

	/**
	 * Order actions for processed
	 *
	 * @param $actions
	 * @param $order
	 *
	 * @return mixed
	 */
	function woocommerce_admin_order_actions($actions, $order){
		//print_r($actions);
		if ( $order->has_status( array( 'processing', 'completed' ) ) ) {

			// Get Order ID (compatibility all WC versions)
			$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
			// Set the action button
			$actions['processed'] = array(
				'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=processed&order_id=' . $order_id ),
					'woocommerce-mark-order-status' ),
				'name'   => __( 'Mark as processed', 'tmsm-woocommerce-vouchers' ),
				'action' => "view processed", // keep "view" class for a clean button CSS
			);
		}
		return $actions;
	}

	/**
	 * Action when order goes from completed to processed
	 *
	 * @param $order_id int
	 * @param $order WC_Order
	 */
	function status_completed_to_processed($order_id, $order){

	}

	/**
	 * Get list of statuses which are consider 'paid'.
	 *
	 * @param $statuses array
	 * @return array
	 */
	function woocommerce_order_is_paid_statuses($statuses){
		$statuses[] = 'processed';
		return $statuses;
	}

	/**
	 * WooCommerce reports with custom statuts processed as paid status
	 *
	 * @param $statuses array
	 *
	 * @return array
	 */
	function woocommerce_reports_order_statuses($statuses){
		if(isset($statuses)){
			if(in_array('completed', $statuses) || in_array('processing', $statuses)){
				array_push( $statuses, 'processed');
			}
		}
		return $statuses;
	}

	/**
	 * Add a custom product tab "voucher"
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $tabs
	 *
	 * @return mixed $tabs
	 */
	function woocommerce_product_data_tabs_voucher( $tabs ) {
		$tabs['voucher'] = array(
			'label'		=> __( 'Voucher', 'tmsm-woocommerce-vouchers' ),
			'target'	=> 'voucher_options',
		);
		return $tabs;
	}



	/**
	 * Tab content of tab "voucher"
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function woocommerce_product_data_panels_voucher() {
		global $post;

		// Note the 'id' attribute needs to match the 'target' parameter set above
		?><div id='voucher_options' class='panel woocommerce_options_panel hidden'><?php
		?>

		<div class='options_group'>

			<?php
			/*woocommerce_wp_checkbox( array(
				'id'            => '_my_custom_field',
				'label'         => __( 'My Custom Field Label', 'my_text_domain' ),
				'description'   => __( 'My Custom Field Description', 'my_text_domain' ),
				'default'  		=> '0',
				'desc_tip'    	=> false,
			) );*/
			woocommerce_wp_text_input( array(
				'id'            => '_tmsm_woocommerce_vouchers_expiremonths',
				'wrapper_class' => 'show_if_voucher',
				'label'     => __( 'Voucher expires', 'tmsm-woocommerce-vouchers' ),
				'description'     => __( 'months', 'tmsm-woocommerce-vouchers' ),
				'type'              => 'number',
				'placeholder'       => get_option('tmsm_woocommerce_vouchers_expiremonths'),
				'default'  		=> '',
				'desc_tip'    	=> true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_tmsm_woocommerce_vouchers_expirydate',
				'wrapper_class' => 'show_if_voucher',
				'label'     => __( 'Voucher date expiry', 'tmsm-woocommerce-vouchers' ),
				'description'     => __( 'Format: YYYY-MM-DD, leave empty for default duration', 'tmsm-woocommerce-vouchers' ),
				'type'              => 'text',
				'placeholder'       => __( 'YYYY-MM-DD', 'tmsm-woocommerce-vouchers' ),
				'default'  		=> '',
				'desc_tip'    	=> true,
			) );

			$localbusinesses = get_posts(['post_type' => 'localbusiness', 'numberposts' => -1]);
			if(is_array($localbusinesses)){
				$localbusinesses_array = [];
				foreach($localbusinesses as $localbusiness){
					$localbusinesses_array[$localbusiness->ID] = $localbusiness->post_title;
				}
				woocommerce_wp_select( array(
						'id'      => '_tmsm_woocommerce_vouchers_localbusiness',
						'label'   => __( 'Local Business', 'tmsm-woocommerce-vouchers' ),
						'options' => $localbusinesses_array
					)
				);
				woocommerce_wp_textarea_input( array(
						'id'      => '_tmsm_woocommerce_vouchers_description',
						'label'   => __( 'Product description', 'tmsm-woocommerce-vouchers' ),
					)
				);

		   }





			?>

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
	 * @since 1.0.0
	 *
	 * @param $post_id
	 *
	 * @return void
	 */
	function woocommerce_process_product_save_voucher_options( $post_id ) {
		$is_voucher = isset( $_POST['_voucher'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_voucher', $is_voucher );

		$_tmsm_woocommerce_vouchers_expiremonths = null;
		if(!empty( $_POST['_tmsm_woocommerce_vouchers_expiremonths'])){
			$_tmsm_woocommerce_vouchers_expiremonths = absint( $_POST['_tmsm_woocommerce_vouchers_expiremonths'] );
		}
		if ( isset( $_POST['_tmsm_woocommerce_vouchers_expiremonths'] ) ) :
			update_post_meta( $post_id, '_tmsm_woocommerce_vouchers_expiremonths', wc_clean($_tmsm_woocommerce_vouchers_expiremonths) );
		endif;

		if ( isset( $_POST['_tmsm_woocommerce_vouchers_expirydate'] ) ) :
			update_post_meta( $post_id, '_tmsm_woocommerce_vouchers_expirydate', wc_clean( $_POST['_tmsm_woocommerce_vouchers_expirydate'] ) );
		endif;

		if ( isset( $_POST['_tmsm_woocommerce_vouchers_localbusiness'] ) ) :
			update_post_meta( $post_id, '_tmsm_woocommerce_vouchers_localbusiness', absint( $_POST['_tmsm_woocommerce_vouchers_localbusiness'] ) );
		endif;

		if ( isset( $_POST['_tmsm_woocommerce_vouchers_description'] ) ) :
			update_post_meta( $post_id, '_tmsm_woocommerce_vouchers_description', esc_html( $_POST['_tmsm_woocommerce_vouchers_description'] ) );
		endif;

		/*
		$allow_personal_message = isset( $_POST['_allow_personal_message'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_allow_personal_message', $allow_personal_message );

		if ( isset( $_POST['_valid_for_days'] ) ) :
			update_post_meta( $post_id, '_valid_for_days', absint( $_POST['_valid_for_days'] ) );
		endif;
		*/
	}

	/**
	 * Definition for product type "voucher"
	 *
	 * @since 1.0.0
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
	 * Checkbox for product variation type "voucher"
	 *
	 * @since 1.0.0
	 *
	 * @param $loop
	 * @param $variation_data
	 * @param $variation
	 *
	 * @return void
	 */
	public function woocommerce_variation_options_voucher( $loop, $variation_data, $variation ) {
		$is_voucher = ( isset( $variation_data['_voucher'] ) && 'yes' == $variation_data['_voucher'][0] );
		echo '<label class="notips"><input type="checkbox" class="checkbox variable_is_voucher" name="variable_is_voucher[' . $loop . ']"' . checked( true, $is_voucher, false ) . '> '.__( 'Voucher', 'tmsm-woocommerce-vouchers' ).'</label>' . PHP_EOL;
	}

	/**
	 * Save product variation type "voucher"
	 *
	 * @since 1.0.0
	 *
	 * @param integer $post_id
	 * @param integer $i
	 *
	 * @return void
	 */
	public function woocommerce_save_product_variation_voucher( $post_id, $i ) {
		$is_voucher = isset( $_POST['variable_is_voucher'][ $i  ] ) ? 'yes' : 'no';
		update_post_meta( $post_id , '_voucher', $is_voucher );
	}


	/**
	 * Settings section
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $sections
	 *
	 * @return mixed $sections
	 */
	function woocommerce_settings_tabs_array_vouchers( $sections ) {

		$sections['vouchers'] = __( 'Vouchers', 'tmsm-woocommerce-vouchers' );
		return $sections;

	}

	/**
	 * Include settings class
	 *
	 * @since 1.0.0
	 *
	 * @param $settings
	 *
	 * @return array
	 */
	function woocommerce_get_settings_pages_vouchers($settings) {
		$settings[] = include( plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-tmsm-woocommerce-vouchers-settings.php' );
		return $settings; // Return
	}

	/**
	 * Hide order item meta from Order
	 *
	 * @since 1.0.0
	 *
	 * @param array $item_array
	 *
	 * @return array $item_array
	 */
	public function woocommerce_hidden_order_itemmeta($item_array = []){
		$item_meta[] = '_virtual';
		$item_meta[] = '_voucher';

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

			// lastname
			if($meta->key == '_recipientlastname' && !empty($meta->value)){
				$meta->display_key = __('Recipient last name', 'tmsm-woocommerce-vouchers');
			}

			// address
			if($meta->key == '_recipientaddress' && !empty($meta->value)){
				$meta->display_key = __('Recipient address', 'tmsm-woocommerce-vouchers');
			}

			// zipcode
			if($meta->key == '_recipientzipcode' && !empty($meta->value)){
				$meta->display_key = __('Recipient zipcode', 'tmsm-woocommerce-vouchers');
			}

			// city
			if($meta->key == '_recipientcity' && !empty($meta->value)){
				$meta->display_key = __('Recipient city', 'tmsm-woocommerce-vouchers');
			}

			// country
			if($meta->key == '_recipientcountry' && !empty($meta->value)){
				$meta->display_key = __('Recipient country', 'tmsm-woocommerce-vouchers');
				$full_country = ( isset( WC()->countries->countries[ $meta->value ] ) ) ? WC()->countries->countries[ $meta->value ] : $meta->value;
				$meta->display_value = $full_country;
			}

			// mobilephone
			if($meta->key == '_recipientmobilephone' && !empty($meta->value)){
				$meta->display_key = __('Recipient mobile phone', 'tmsm-woocommerce-vouchers');
			}

			// email
			if($meta->key == '_recipientemail' && !empty($meta->value)){
				$meta->display_key = __('Recipient email', 'tmsm-woocommerce-vouchers');
			}

			// vouchercode
			if($meta->key == '_vouchercode' && !empty($meta->value)){
				$meta->display_key = __('Voucher code', 'tmsm-woocommerce-vouchers');
			}

			// expirydate
			if($meta->key == '_expirydate' && !empty($meta->value)){
				$meta->display_key = __('Expiry date', 'tmsm-woocommerce-vouchers');
				$meta->display_value = date_i18n( get_option( 'date_format' ), strtotime( $meta->value ) );
			}

		}

		return $formatted_meta;
	}

	/**
	 * Plugin links in plugins list
	 *
	 * @param $links
	 *
	 * @return array
	 */
	function plugin_action_links( $links ) {
		$plugin_links = [
			'<a href="' . 'admin.php?page=wc-settings&tab=tmsmvouchers">' . __( 'Settings' ) . '</a>',
			'<a href="' . 'https://github.com/thermesmarins/tmsm-woocommerce-vouchers' . '" target="_blank">' . __( 'Github', 'tmsm-woocommerce-vouchers' ) . '</a>',
		];
		return array_merge( $plugin_links, $links );
	}



	/**
	 * Product Save
	 *
	 * @param $post_id
	 */
	public function save_post($post_id){

		$product = wc_get_product($post_id);
		if($product && $product->is_type('variable')){

			$variable = new WC_Product_Variable( $product->get_id());
			$variations = $variable->get_available_variations();

			$variable_has_voucher = false;
			if(is_array($variations)){
				foreach($variations as $variation){
					$is_voucher = get_post_meta( $variation['variation_id'], '_voucher', true ) == 'yes';
					if($is_voucher){
						$variable_has_voucher = true;
					}
				}
			}
			if($variable_has_voucher){
				//$updated = update_post_meta( $product->get_id(), '_sold_individually','yes' );
			}

		}

		if($product && !$product->is_type('variable')){
			$is_voucher = get_post_meta($product->get_id(), '_voucher', true ) == 'yes';
			if($is_voucher){
				//$updated = update_post_meta( $product->get_id(), '_sold_individually','yes' );
			}
		}

	}

	/**
	 * "Virtual only" column content
	 *
	 * @param $column
	 * @param $post_id
	 */
	function shop_order_posts_custom_column_virtualonly( $column, $post_id )
	{
		global $post, $the_order;

		if ( empty( $the_order ) || $the_order->get_id() !== $post->ID ) {
			$the_order = wc_get_order( $post->ID );
		}

		switch ( $column ) {
			case 'shipping_address':
				if ( !($address = $the_order->get_formatted_shipping_address() ) && $the_order->is_paid()) {
					echo ' <span style="color: #73a724"><span class="dashicons dashicons-download" style="margin-left: -17px;"></span>'.__( 'Virtual only', 'tmsm-woocommerce-vouchers' ).'</span>';
				}
				break;

		}
	}

}
