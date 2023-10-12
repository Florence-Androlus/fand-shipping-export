<?php
// Assurez-vous d'inclure les fichiers WooCommerce et d'initialiser WordPress si nécessaire
// Par exemple, si vous n'êtes pas déjà dans un contexte WordPress, vous pouvez ajouter les lignes suivantes :
// require_once('wp-load.php');
// global $wpdb;

if (isset($_GET['selectedValue'])) {
    $selectedValue = intval($_GET['selectedValue']);

    // Obtenez les identifiants de publication des commandes WooCommerce
    $commande_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'shop_order'");

    $filteredOrders = array();

    foreach ($commande_ids as $commande_id) {
        $commande = wc_get_order($commande_id);
        $date_commande = $commande->get_date_created();
        $numero_commande = $commande->get_order_number();
        $statut_commande = $commande->get_status();

        // Comparez la date de commande avec la date sélectionnée
        $selectedDate = date('Y-m-d', strtotime('+' . $selectedValue . ' days'));
        $orderDate = date('Y-m-d', strtotime($date_commande));

        if ($selectedDate === $orderDate) {
            $filteredOrders[] = array(
                'commande_id' => $commande_id,
                'numero_commande' => $numero_commande,
                'date_commande' => $date_commande,
                'statut_commande' => $statut_commande,
            );
        }
    }

    // Renvoyez les données au format JSON
    header('Content-Type: application/json');
    echo json_encode($filteredOrders);
} else {
    // Si la valeur sélectionnée n'est pas définie, renvoyez une réponse vide ou une erreur
    http_response_code(400);
    echo json_encode(array('message' => 'Valeur de sélection non définie'));
}
?>
