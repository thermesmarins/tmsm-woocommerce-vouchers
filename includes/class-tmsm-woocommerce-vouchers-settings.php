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

				$this->id    = 'tmsmvouchers';
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
					''         => __( 'Voucher settings', 'tmsm-woocommerce-vouchers' ),
					'recipient'         => __( 'Recipient', 'tmsm-woocommerce-vouchers' ),
					'template' => __( 'Template', 'tmsm-woocommerce-vouchers' )
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

				if ( 'recipient' == $current_section ) {

					$settings = array(

						array(
							'name' => __( 'Recipient fields', 'tmsm-woocommerce-vouchers' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'tmsm_woocommerce_vouchers_recipient',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientoptionnal',
							'name'     => __( 'Recipient optionnal', 'tmsm-woocommerce-vouchers' ),
							'desc'     => '<p class="description">'.__( 'If checked, the customer is not obliged to set a recipient' ).'</p>',
							'default'  => 'yes',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientfirstname',
							'name'     => __( 'First name asked', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),
						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientfirstnamerequired',
							'name'     => __( 'First name required', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientlastname',
							'name'     => __( 'Last name asked', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),
						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientlastnamerequired',
							'name'     => __( 'Last name required', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientbirthdate',
							'name'     => __( 'Birthdate asked', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),
						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientbirthdaterequired',
							'name'     => __( 'Birthdate required', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipienttitle',
							'name'     => __( 'Title asked', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),
						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipienttitlerequired',
							'name'     => __( 'Title required', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientaddress',
							'name'     => __( 'Address asked', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),
						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientaddressrequired',
							'name'     => __( 'Address required', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientzipcode',
							'name'     => __( 'Zipcode asked', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),
						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientzipcoderequired',
							'name'     => __( 'Zipcode required', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientcity',
							'name'     => __( 'City asked', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),
						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientcityrequired',
							'name'     => __( 'City required', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientcountry',
							'name'     => __( 'Country asked', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),
						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientcountryrequired',
							'name'     => __( 'Country required', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),


						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientmobilephone',
							'name'     => __( 'Mobile phone asked', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),
						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientmobilephonerequired',
							'name'     => __( 'Mobile phone required', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientemail',
							'name'     => __( 'Email asked', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),
						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientemailrequired',
							'name'     => __( 'Email required', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientmessage',
							'name'     => __( 'Message asked', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),
						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientmessagerequired',
							'name'     => __( 'Message required', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientsenddate',
							'name'     => __( 'Sent date asked', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),
						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_recipientsenddaterequired',
							'name'     => __( 'Send date required', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type' => 'sectionend',
							'id'   => 'tmsm_woocommerce_vouchers_template'
						),

					);

				}
				else if ( 'template' == $current_section ) {

					$settings = array(

						array(
							'name' => __( 'Template', 'tmsm-woocommerce-vouchers' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'tmsm_woocommerce_vouchers_template',
						),

						array(
							'type' => 'sectionend',
							'id'   => 'tmsm_woocommerce_vouchers_template'
						),

					) ;

				} else {
					// Gift notification schedule time options		
					$all_schedule_time_options = array(
						''  => __( 'Default', 'tmsm-woocommerce-vouchers' ),
						'0'	=> __( '12 AM', 'tmsm-woocommerce-vouchers' ),
						'1'	=> __( '1 AM', 'tmsm-woocommerce-vouchers' ),
						'2'	=> __( '2 AM', 'tmsm-woocommerce-vouchers' ),
						'3'	=> __( '3 AM', 'tmsm-woocommerce-vouchers' ),
						'4'	=> __( '4 AM', 'tmsm-woocommerce-vouchers' ),
						'5'	=> __( '5 AM', 'tmsm-woocommerce-vouchers' ),
						'6'	=> __( '6 AM', 'tmsm-woocommerce-vouchers' ),
						'7'	=> __( '7 AM', 'tmsm-woocommerce-vouchers' ),
						'8'	=> __( '8 AM', 'tmsm-woocommerce-vouchers' ),
						'9'	=> __( '9 AM', 'tmsm-woocommerce-vouchers' ),
						'10'=> __( '10 AM', 'tmsm-woocommerce-vouchers' ),
						'11'=> __( '11 AM', 'tmsm-woocommerce-vouchers' ),
						'12'=> __( '12 PM', 'tmsm-woocommerce-vouchers' ),
						'13'=> __( '1 PM', 'tmsm-woocommerce-vouchers' ),
						'14'=> __( '2 PM', 'tmsm-woocommerce-vouchers' ),
						'15'=> __( '3 PM', 'tmsm-woocommerce-vouchers' ),
						'16'=> __( '4 PM', 'tmsm-woocommerce-vouchers' ),
						'17'=> __( '5 PM', 'tmsm-woocommerce-vouchers' ),
						'18'=> __( '6 PM', 'tmsm-woocommerce-vouchers' ),
						'19'=> __( '7 PM', 'tmsm-woocommerce-vouchers' ),
						'20'=> __( '8 PM', 'tmsm-woocommerce-vouchers' ),
						'21'=> __( '9 PM', 'tmsm-woocommerce-vouchers' ),
						'22'=> __( '10 PM', 'tmsm-woocommerce-vouchers' ),
						'23'=> __( '11 PM', 'tmsm-woocommerce-vouchers' ),
					);
					
					$settings = array(
						array(
							'name' => __( 'Voucher settings', 'tmsm-woocommerce-vouchers' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'tmsm_woocommerce_vouchers_settings',
						),


						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_physical',
							'name'     => __( 'Available for physical products', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_virtual',
							'name'     => __( 'Available for virtual products', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type'     => 'text',
							'id'       => 'tmsm_woocommerce_vouchers_recipientheading',
							'name'     => __( 'Recipient Heading', 'tmsm-woocommerce-vouchers' ),
							'desc'     => '<p class="description">'.__( 'Text introducing the recipient form' ).'</p>',
							'default'  => __( 'Who to give this voucher to?', 'tmsm-woocommerce-vouchers' ),
						),

						array(
							'type'     => 'checkbox',
							'id'       => 'tmsm_woocommerce_vouchers_attachemail',
							'name'     => __( 'Attach PDF voucher to email notification', 'tmsm-woocommerce-vouchers' ),
							'default'  => 'yes',
						),

						array(
							'type'		=> 'select',
							'id'		=> 'tmsm_woocommerce_vouchers_notificationtime',
							'name'		=> __( 'Select Time for Gift Notification Email', 'tmsm-woocommerce-vouchers' ),
							'desc'		=> '<p class="description">'.__( 'It will send gift notification email at selected time.', 'tmsm-woocommerce-vouchers' ).'</p>',
							'class'		=> 'wc-enhanced-select',
							'options'	=> $all_schedule_time_options
						),

						array(
							'type'     => 'text',
							'id'       => 'tmsm_woocommerce_vouchers_vouchercodeformat',
							'name'     => __( 'Voucher code format', 'tmsm-woocommerce-vouchers' ),
							'desc'     => ''.
							              '<p class="description">'.
							              __( 'The available tags are:', 'tmsm-woocommerce-vouchers'). '<br>'.
							              __( '<code>{sku}</code> - SKU of product or variation of product', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              __( '<code>{uses}</code> - number of uses zero filled of 6 characters', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              __( '<code>{order_id}</code> - order ID', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              '</p>',
							'default'  => '{sku}-{uses}',
							'class'  => 'regular-text',
						),

						array(
							'type'     => 'text',
							'id'       => 'tmsm_woocommerce_vouchers_downloadfilename',
							'name'     => __( 'Download PDF File Name', 'tmsm-woocommerce-vouchers' ),
							'desc'     => __( '.pdf', 'tmsm-woocommerce-vouchers' ).
							              '<p class="description">'.__( 'Enter the PDF file name. This file name will be used when users download a PDF of voucher codes on frontend.', 'tmsm-woocommerce-vouchers'). ' '.
							              __( 'The available tags are:', 'tmsm-woocommerce-vouchers'). '<br>'.
							              __( '<code>{item_id}</code> - item ID', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              __( '<code>{product_id}</code> - product ID', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              __( '<code>{order_id}</code> - order ID', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              __( '<code>{unique_string}</code> - unique string', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              __( '<code>{current_date}</code> - the current date YYYYMMDD', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              '</p>',
							'default'  => 'voucher-{current_date}-{unique_string}',
							'class'  => 'regular-text',
						),

						array(
							'type'     => 'text',
							'id'       => 'tmsm_woocommerce_vouchers_attachmentfilename',
							'name'     => __( 'Attachment PDF File Name', 'tmsm-woocommerce-vouchers' ),
							'desc'     => __( '.pdf', 'tmsm-woocommerce-vouchers' ).
							              '<p class="description">'.__( 'Enter the PDF file name. This file name will be used when users download a PDF of voucher codes from the email attachment.', 'tmsm-woocommerce-vouchers'). ' '.
							              __( 'The available tags are:', 'tmsm-woocommerce-vouchers'). '<br>'.
							              __( '<code>{item_id}</code> - item ID', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              __( '<code>{product_id}</code> - product ID', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              __( '<code>{order_id}</code> - order ID', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              __( '<code>{voucher_code}</code> - voucher code', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              __( '<code>{unique_string}</code> - unique string', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              __( '<code>{current_date}</code> - the current date YYYYMMDD', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              __( '<code>{purchase_date}</code> - the date of the payment YYYYMMDD', 'tmsm-woocommerce-vouchers' ). '<br>'.
							              '</p>',
							'default'  => 'voucher-{current_date}-{unique_string}',
							'class'  => 'regular-text',
						),
						array(
							'type'     => 'text',
							'id'       => 'tmsm_woocommerce_vouchers_expiremonths',
							'name'     => __( 'Voucher expires', 'tmsm-woocommerce-vouchers' ),
							'desc'     => __( 'months', 'tmsm-woocommerce-vouchers' ),
							'default'  => '12',
						),
					    
						/*
						array(
							'name' => __( 'Important Stuff', 'tmsm-woocommerce-vouchers' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'tmsm_woocommerce_vouchers_important_options',
						),

						array(
							'type'     => 'select',
							'id'       => 'tmsm_woocommerce_vouchers_select_1',
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
*/
						array(
							'type' => 'sectionend',
							'id'   => 'tmsm_woocommerce_vouchers_settings'
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