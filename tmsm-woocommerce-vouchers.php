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
 * Version:           1.4.1
 * Author:            Nicolas Mollet
 * Author URI:        https://github.com/thermesmarins/
 * Requires PHP:      7.0
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tmsm-woocommerce-vouchers
 * Domain Path:       /languages
 * Github Plugin URI: https://github.com/thermesmarins/tmsm-woocommerce-vouchers/
 * Github Branch:     master
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TMSM_WOOCOMMERCE_VOUCHERS_VERSION', '1.4.0' );

$upload_dir		= wp_upload_dir();
$upload_path	= isset( $upload_dir['basedir'] ) ? $upload_dir['basedir'].'/' : ABSPATH;
define( 'TMSMWOOCOMMERCEVOUCHERS_UPLOADDIR' , $upload_path . 'woocommerce_uploads/vouchers/' ); // Voucher upload dir
define( 'TMSMWOOCOMMERCEVOUCHERS_PLUGINDIR', plugin_dir_path( __FILE__ ) );

define( 'TMSMWOOCOMMERCEVOUCHERS_ACF_PATH', plugin_dir_path( __FILE__ ) . 'includes/advanced-custom-fields/' );
define( 'TMSMWOOCOMMERCEVOUCHERS_ACF_URL', plugin_dir_url( __FILE__ ) . 'includes/advanced-custom-fields/' );
include_once( TMSMWOOCOMMERCEVOUCHERS_ACF_PATH . 'acf.php' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tmsm-woocommerce-vouchers-activator.php
 */
function activate_tmsm_woocommerce_vouchers() {
	require_once TMSMWOOCOMMERCEVOUCHERS_PLUGINDIR . 'includes/class-tmsm-woocommerce-vouchers-activator.php';
	Tmsm_Woocommerce_Vouchers_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tmsm-woocommerce-vouchers-deactivator.php
 */
function deactivate_tmsm_woocommerce_vouchers() {
	require_once TMSMWOOCOMMERCEVOUCHERS_PLUGINDIR . 'includes/class-tmsm-woocommerce-vouchers-deactivator.php';
	Tmsm_Woocommerce_Vouchers_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tmsm_woocommerce_vouchers' );
register_deactivation_hook( __FILE__, 'deactivate_tmsm_woocommerce_vouchers' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require TMSMWOOCOMMERCEVOUCHERS_PLUGINDIR . 'includes/class-tmsm-woocommerce-vouchers.php';

// Autoloading via composer
require_once __DIR__ . '/vendor/autoload.php';

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
