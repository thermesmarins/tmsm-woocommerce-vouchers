<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/thermesmarins/
 * @since      1.0.0
 *
 * @package    Tmsm_Woocommerce_Vouchers
 * @subpackage Tmsm_Woocommerce_Vouchers/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Tmsm_Woocommerce_Vouchers
 * @subpackage Tmsm_Woocommerce_Vouchers/includes
 * @author     Nicolas MOLLET <nmollet@thalassotherapie.com>
 */
class Tmsm_Woocommerce_Vouchers_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'tmsm-woocommerce-vouchers',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
