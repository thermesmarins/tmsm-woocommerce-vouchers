<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/thermesmarins/
 * @since             1.0.0
 * @package           Tmsm_Woocommerce_Vouchers
 *
 * @wordpress-plugin
 * Plugin Name:       TMSM WooCommerce Vouchers
 * Plugin URI:        https://github.com/thermesmarins/tmsm-woocommerce-vouchers/
 * Description:       WooCommerce Vouchers for Thermes Marins de Saint-Malo
 * Version:           1.0.0
 * Author:            Nicolas MOLLET
 * Author URI:        https://github.com/thermesmarins/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tmsm-woocommerce-vouchers
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tmsm-woocommerce-vouchers-activator.php
 */
function activate_tmsm_woocommerce_vouchers() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tmsm-woocommerce-vouchers-activator.php';
	Tmsm_Woocommerce_Vouchers_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tmsm-woocommerce-vouchers-deactivator.php
 */
function deactivate_tmsm_woocommerce_vouchers() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tmsm-woocommerce-vouchers-deactivator.php';
	Tmsm_Woocommerce_Vouchers_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tmsm_woocommerce_vouchers' );
register_deactivation_hook( __FILE__, 'deactivate_tmsm_woocommerce_vouchers' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tmsm-woocommerce-vouchers.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tmsm_woocommerce_vouchers() {

	$plugin = new Tmsm_Woocommerce_Vouchers();
	$plugin->run();

}
run_tmsm_woocommerce_vouchers();
