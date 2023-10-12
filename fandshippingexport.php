<?php
/**
 * Plugin Name: Fand Shipping Export
 * Description: This plugin allows you to export order information to Excel files for shipping purposes.
 * Version: 1.0.0
 * Author: Florence Androlus
 * Text Domain: Fan-develop
 * Licence: GNU General Public License v3.0
 * URL de la licence: https://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || exit;
require __DIR__ . '/vendor/autoload.php';

/* Chemin vers ce fichier dans une constante
* => sera utile pour les hook d'activation et désactivation
*/
define( 'FDSE_MAIN_FILE', __FILE__);
define( 'FDSE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FDSE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/* If this file is called directly, abort.*/
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once FDSE_PLUGIN_DIR . 'plugin.php';
//require_once FDSE_PLUGIN_DIR . 'fieldcheckoutgiftcard.php';


// Fonction pour supprimer les rôles "order_manager / order_picker"
function wpfse_remove_order_role() {
    remove_role( 'order_manager' );
    remove_role( 'order_picker' );
}
register_deactivation_hook( __FILE__, 'wpfse_remove_order_role' );