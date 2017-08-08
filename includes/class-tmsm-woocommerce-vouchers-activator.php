<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/thermesmarins/
 * @since      1.0.0
 *
 * @package    Tmsm_Woocommerce_Vouchers
 * @subpackage Tmsm_Woocommerce_Vouchers/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tmsm_Woocommerce_Vouchers
 * @subpackage Tmsm_Woocommerce_Vouchers/includes
 * @author     Nicolas MOLLET <nmollet@thalassotherapie.com>
 */
class Tmsm_Woocommerce_Vouchers_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if ( ! self::compatible_version() ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( __( 'My Plugin requires WordPress 3.7 or higher!', 'my-plugin' ) );
        }

	}

	/**
	 * The plugin requires WooCommerce version 3.0.0 minimum
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	static function compatible_version() {
		if (version_compare(WOOCOMMERCE_VERSION, "3.0.0") == -1){
             return false;
         }
        return true;
    }


	
}
