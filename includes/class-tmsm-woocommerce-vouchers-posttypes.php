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
 * @author     Nicolas MOLLET <nmollet@thalassotherapie.com>
 */
class Tmsm_Woocommerce_Vouchers_Posttypes {

	/**
	 * Register post type: Local Businesses
	 *
	 */
	public function register_post_type_localbusiness(){
		$labels = array(
			'name'                  => _x( 'Local Businesses', 'Post type general name', 'thalasso' ),
			'singular_name'         => _x( 'Local Business', 'Post type singular name', 'thalasso' ),
			'menu_name'             => _x( 'Local Businesses', 'Admin Menu text', 'thalasso' ),
			'name_admin_bar'        => _x( 'Local Business', 'Add New on Toolbar', 'thalasso' ),
			'add_new'               => __( 'Add New', 'thalasso' ),
			'add_new_item'          => __( 'Add New Local Business', 'thalasso' ),
			'new_item'              => __( 'New Local Business', 'thalasso' ),
			'edit_item'             => __( 'Edit Local Business', 'thalasso' ),
			'view_item'             => __( 'View Local Business', 'thalasso' ),
			'all_items'             => __( 'Local Businesses', 'thalasso' ),
			'search_items'          => __( 'Search Local Businesses', 'thalasso' ),
			'parent_item_colon'     => __( 'Parent Local Businesses:', 'thalasso' ),
			'not_found'             => __( 'No local Businesses found.', 'thalasso' ),
			'not_found_in_trash'    => __( 'No local Businesses found in Trash.', 'thalasso' ),
			//'featured_image'        => _x( 'Local Business Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'thalasso' ),
			//'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'thalasso' ),
			//'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'thalasso' ),
			//'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'thalasso' ),
			//'archives'              => _x( 'Local Business archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'thalasso' ),
			//'insert_into_item'      => _x( 'Insert into local business', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'thalasso' ),
			//'uploaded_to_this_item' => _x( 'Uploaded to this local business', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'thalasso' ),
			//'filter_items_list'     => _x( 'Filter local Businesses list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'thalasso' ),
			//'items_list_navigation' => _x( 'Local Businesses list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'thalasso' ),
			//'items_list'            => _x( 'Local Businesses list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'thalasso' ),
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
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'menu_icon'          => 'dashicons-store',
		);

		register_post_type( 'localbusiness', $args );
	}
}
