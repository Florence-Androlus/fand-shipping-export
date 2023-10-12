<?php
namespace fdse;
use fdse\templates\templates;
use fdse\includes\Export_File;

class WP_Fand_Shipping_Export {

    public function __construct() {
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css', array(), '5.0.2', 'all');
        wp_enqueue_script('bootstrap-bundle', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.0.2', true);
    
        add_action('init', [$this,'wpfse_create_order_roles']);
        add_action( 'admin_menu', [$this,'wpfse_add_menu_page'] );
	}


// Creation des rôles "order_manager / order_picker"
function wpfse_create_order_roles() {
    // Capacités pour le gestionnaire de commande
    $order_manager_capabilities = array(
        'read' => true,
        'edit_posts' => false,
        'delete_posts' => false,
        'manage_woocommerce' => true,
        // Ajoutez d'autres capacités spécifiques au gestionnaire de commande si nécessaire
    );

    // Capacités pour le préparateur de commande
    $order_picker_capabilities = array(
        'read' => true,
        'edit_posts' => false,
        'delete_posts' => false,
        'manage_woocommerce' => true,
        // Ajoutez d'autres capacités spécifiques au préparateur de commande si nécessaire
    );

    // Créez les rôles en utilisant les capacités spécifiques définies ci-dessus
    add_role( 'order_manager', 'Gestionnaire de commande', $order_manager_capabilities );
    add_role( 'order_picker', 'Préparateur de commande', $order_picker_capabilities );
}



// Add custom menu page to display order information
function wpfse_add_menu_page() {
    if (current_user_can('order_picker') || current_user_can( 'administrator' ) || current_user_can( 'order_manager' )) {
        add_menu_page(
            'Fand Shipping Export',
            'Fand Shipping Export',
            'manage_woocommerce',
            'fand-shipping-export-plugin',
            [$this, 'fand_shipping_list'],
            'dashicons-download',
            90
        );
    }
}



function fand_shipping_list() {

    if (current_user_can('order_picker')){
        Templates::Template_order_picker();
    }

    if ( current_user_can( 'administrator' ) || current_user_can( 'order_manager' )) {
        Templates::Template_order_manager();
        Export_File::export_file();

    }
   
}

}

new WP_Fand_Shipping_Export();
