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
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! class_exists( 'WC_Settings_Vouchers' ) ) :
		/**
		 * Settings class
		 *
		 * @since 1.0.0
		 */
		class WC_Settings_Vouchers extends WC_Settings_Page {

			/**
			 * Setup settings class
			 *
			 * @since  1.0
			 */
			public function __construct() {

				$this->id    = 'vouchers';
				$this->label = __( 'Vouchers', 'tmsm-woocommerce-vouchers' );

				add_filter( 'woocommerce_settings_tabs_array',        array( $this, 'add_settings_page' ), 20 );
				add_action( 'woocommerce_settings_' . $this->id,      array( $this, 'output' ) );
				add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
				add_action( 'woocommerce_sections_' . $this->id,      array( $this, 'output_sections' ) );
			}


			/**
			 * Get sections
			 *
			 * @return array
			 */
			public function get_sections() {

				$sections = array(
					''         => __( 'Section 1', 'tmsm-woocommerce-vouchers' ),
					'second' => __( 'Section 2', 'tmsm-woocommerce-vouchers' )
				);

				return $sections;
			}

			/**
			 * Get settings array
			 *
			 * @since 1.0.0
			 * @param string $current_section Optional. Defaults to empty string.
			 * @return array Array of settings
			 */
			public function get_settings( $current_section = '' ) {

				if ( 'second' == $current_section ) {

					$settings = array(

						array(
							'name' => __( 'Group 1', 'tmsm-woocommerce-vouchers' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'myplugin_group1_options',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'myplugin_checkbox_1',
							'name'     => __( 'Do a thing?', 'tmsm-woocommerce-vouchers' ),
							'desc'     => __( 'Enable to do something', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'no',
						),

						array(
							'type' => 'sectionend',
							'id'   => 'myplugin_group1_options'
						),

						array(
							'name' => __( 'Group 2', 'tmsm-woocommerce-vouchers' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'myplugin_group2_options',
						),

						array(
							'type'     => 'select',
							'id'       => 'myplugin_select_1',
							'name'     => __( 'What should happen?', 'tmsm-woocommerce-vouchers' ),
							'options'  => array(
								'something' => __( 'Something', 'tmsm-woocommerce-vouchers' ),
								'nothing' 	=> __( 'Nothing', 'tmsm-woocommerce-vouchers' ),
								'idk'    	=> __( 'IDK', 'tmsm-woocommerce-vouchers' ),
							),
							'class'    => 'wc-enhanced-select',
							'desc_tip' => __( 'Don\'t ask me!', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'idk',
						),

						array(
							'type' => 'sectionend',
							'id'   => 'myplugin_group2_options'
						),

					) ;

				} else {

					$settings = array(

						array(
							'name' => __( 'Important Stuff', 'tmsm-woocommerce-vouchers' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'myplugin_important_options',
						),

						array(
							'type'     => 'select',
							'id'       => 'myplugin_select_1',
							'name'     => __( 'Choose your favorite', 'tmsm-woocommerce-vouchers' ),
							'options'  => array(
								'vanilla'        => __( 'Vanilla', 'tmsm-woocommerce-vouchers' ),
								'chocolate'		 => __( 'Chocolate', 'tmsm-woocommerce-vouchers' ),
								'strawberry'     => __( 'Strawberry', 'tmsm-woocommerce-vouchers' ),
							),
							'class'    => 'wc-enhanced-select',
							'desc_tip' => __( 'Be honest!', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'vanilla',
						),

						array(
							'type' => 'sectionend',
							'id'   => 'myplugin_important_options'
						),

					) ;

				}

				return $settings;

			}


			/**
			 * Output the settings
			 *
			 * @since 1.0
			 */
			public function output() {

				global $current_section;

				$settings = $this->get_settings( $current_section );
				WC_Admin_Settings::output_fields( $settings );
			}


			/**
			 * Save settings
			 *
			 * @since 1.0
			 */
			public function save() {

				global $current_section;

				$settings = $this->get_settings( $current_section );
				WC_Admin_Settings::save_fields( $settings );
			}
		}


endif;
return new WC_Settings_Vouchers();