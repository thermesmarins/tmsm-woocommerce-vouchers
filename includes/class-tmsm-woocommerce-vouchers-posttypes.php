<?php

/**
 * Define the post types
 *
 * @link       https://github.com/thermesmarins/
 * @since      1.0.0
 *
 * @package    Tmsm_Woocommerce_Vouchers
 * @subpackage Tmsm_Woocommerce_Vouchers/includes
 */

/**
 * Define the post types
 *
 * @since      1.0.0
 * @package    Tmsm_Woocommerce_Vouchers
 * @subpackage Tmsm_Woocommerce_Vouchers/includes
 * @author     Nicolas Mollet <nmollet@thalassotherapie.com>
 */
class Tmsm_Woocommerce_Vouchers_Posttypes {


	/**
	 * Register post type: Local Businesses
	 *
	 */
	public function register_post_type_localbusiness(){
		$labels = array(
			'name'                  => _x( 'Local Businesses', 'Post type general name', 'tmsm-woocommerce-vouchers' ),
			'singular_name'         => _x( 'Local Business', 'Post type singular name', 'tmsm-woocommerce-vouchers' ),
			'menu_name'             => _x( 'Local Businesses', 'Admin Menu text', 'tmsm-woocommerce-vouchers' ),
			'name_admin_bar'        => _x( 'Local Business', 'Add New on Toolbar', 'tmsm-woocommerce-vouchers' ),
			'add_new'               => __( 'Add New', 'tmsm-woocommerce-vouchers' ),
			'add_new_item'          => __( 'Add New Local Business', 'tmsm-woocommerce-vouchers' ),
			'new_item'              => __( 'New Local Business', 'tmsm-woocommerce-vouchers' ),
			'edit_item'             => __( 'Edit Local Business', 'tmsm-woocommerce-vouchers' ),
			'view_item'             => __( 'View Local Business', 'tmsm-woocommerce-vouchers' ),
			'all_items'             => __( 'Local Businesses', 'tmsm-woocommerce-vouchers' ),
			'search_items'          => __( 'Search Local Businesses', 'tmsm-woocommerce-vouchers' ),
			'parent_item_colon'     => __( 'Parent Local Businesses:', 'tmsm-woocommerce-vouchers' ),
			'not_found'             => __( 'No local Businesses found.', 'tmsm-woocommerce-vouchers' ),
			'not_found_in_trash'    => __( 'No local Businesses found in Trash.', 'tmsm-woocommerce-vouchers' ),
			//'featured_image'        => _x( 'Local Business Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'tmsm-woocommerce-vouchers' ),
			//'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'tmsm-woocommerce-vouchers' ),
			//'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'tmsm-woocommerce-vouchers' ),
			//'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'tmsm-woocommerce-vouchers' ),
			//'archives'              => _x( 'Local Business archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'tmsm-woocommerce-vouchers' ),
			//'insert_into_item'      => _x( 'Insert into local business', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'tmsm-woocommerce-vouchers' ),
			//'uploaded_to_this_item' => _x( 'Uploaded to this local business', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'tmsm-woocommerce-vouchers' ),
			//'filter_items_list'     => _x( 'Filter local Businesses list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'tmsm-woocommerce-vouchers' ),
			//'items_list_navigation' => _x( 'Local Businesses list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'tmsm-woocommerce-vouchers' ),
			//'items_list'            => _x( 'Local Businesses list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'tmsm-woocommerce-vouchers' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			//'show_in_menu'       => true,
			'show_in_menu' => 'edit.php?post_type=product',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'localbusiness' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'thumbnail',  ),
			'menu_icon'          => 'dashicons-store',
		);

		register_post_type( 'localbusiness', $args );

		if(function_exists("acf_add_local_field_group"))
		{
			acf_add_local_field_group(array (
				'id' => 'acf_local-business-fields',
				'title' => 'Local Business fields',
				'fields' => array (
					array (
						'key' => 'field_59ef4a92f3956',
						'label' => 'Intro',
						'name' => 'voucher_intro',
						'type' => 'textarea',
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'formatting' => 'br',
					),
					array (
						'key' => 'field_59ef3fd4a2b47',
						'label' => 'Réservation',
						'name' => 'voucher_booking',
						'type' => 'wysiwyg',
						'default_value' => '',
						'toolbar' => 'full',
						'media_upload' => 'no',
					),
					array (
						'key' => 'field_59ef40403119f',
						'label' => 'Info 1',
						'name' => 'voucher_info1',
						'type' => 'wysiwyg',
						'default_value' => '',
						'toolbar' => 'basic',
						'media_upload' => 'no',
					),
					array (
						'key' => 'field_59ef40403119y',
						'label' => 'Info 2',
						'name' => 'voucher_info2',
						'type' => 'wysiwyg',
						'default_value' => '',
						'toolbar' => 'basic',
						'media_upload' => 'no',
					),
					array (
						'key' => 'field_59ef3faf1b610',
						'label' => 'Adresse',
						'name' => 'voucher_address',
						'type' => 'wysiwyg',
						'default_value' => '',
						'toolbar' => 'basic',
						'media_upload' => 'no',
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'localbusiness',
							'order_no' => 0,
							'group_no' => 0,
						),
					),
				),
				'options' => array (
					'position' => 'normal',
					'layout' => 'no_box',
					'hide_on_screen' => array (
						0 => 'the_content',
					),
				),
				'menu_order' => 10,
			));
		}




	}

	/**
	 * Load ACF path
	 *
	 * @param $path
	 *
	 * @return string
	 */
	function acf_path( $path ) {

		$path = plugin_dir_path( dirname( __FILE__ ) ) . '/includes/advanced-custom-fields/';

		// return
		return $path;

	}

	/**
	 * Load ACF dir
	 *
	 * @param $dir
	 *
	 * @return string
	 */
	function acf_dir( $dir ) {

		$dir = plugin_dir_path( dirname( __FILE__ ) ) . '/includes/advanced-custom-fields/';

		// return
		return $dir;

	}

	/**
	 * ACF url
	 *
	 * @param $url
	 *
	 * @return string
	 */
	function acf_url( $url ) {
		return TMSMWOOCOMMERCEVOUCHERS_ACF_URL;
	}

	/**
	 * ACF show admin or not
	 *
	 * @param bool $show_admin
	 *
	 * @return bool
	 */
	function acf_show_admin( $show_admin ) {
		return false;
	}

	/**
	 *
	 */
	function acf_setup()
	{
		include_once( plugin_dir_path(dirname( __FILE__ )) . '/includes/advanced-custom-fields/acf.php' );
	}
}
