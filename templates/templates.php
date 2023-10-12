<?php

namespace fdse\templates;
use fdse\includes\Export_File;

class Templates{

    static public function Template_order_picker() {
        echo '  <div class="wrap">
                <h1 class="wp-heading-inline">Téléchargement du fichier de préparation de commandes</h1>';
        echo '  <br><br><br>
                <a class="page-title-action" href="?page=fand-shipping-export-plugin&cond">export conditionnement</a>';
    }

    static public function Template_order_manager(){
        wp_enqueue_script('select',FDSE_PLUGIN_URL.'assets/js/select.js');

        global $wpdb;
    
        // Obtenez les identifiants de publication des commandes WooCommerce
        $commande_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'shop_order'");

        $selectedDate = isset($_POST['selectedDate']) ? $_POST['selectedDate'] : ''; // Récupérez la date sélectionnée
        isset($_POST['selectedDate']) ?  $tableDisplay = 'table':$tableDisplay = 'none'; // Par défaut, le tableau est masqué

        echo '<form method="post" action="" id="myForm">';

        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">Liste des commandes</h1><br><br><br>';
        
        self::Template_select($selectedDate);

        echo ' <br><br><br> ';

        // Parcourez les identifiants de publication pour accéder aux données de la commande
        self::Template_button($tableDisplay);
        self::Template_header_table($tableDisplay);

        foreach ($commande_ids as $commande_id) {
            $commande = wc_get_order($commande_id);

            $date_commande = $commande->get_date_created();
            $date_commande_format = $date_commande->format('Y-m-d'); // Format de la date au format 'YYYY-MM-DD'

            // Vérifiez si la date de la commande correspond à la date sélectionnée
            if ($date_commande_format == date('Y-m-d', strtotime('+'.$selectedDate.' day'))) {

                // Affichez la commande dans le tableau
                // Accédez aux données de la commande
                //<td> <input class="form-check-input" type="checkbox" value="'.$numero_commande.'" id="Check'.$numero_commande.'" ></td>
                $numero_commande = $commande->get_order_number();
                $date_commande = $commande->get_date_created();
                $statut_commande = $commande->get_status();

                // Vérifiez si la commande est cochée dans le tableau POST
                $idChecked = isset($_POST['selectedOrders']) ? $_POST['selectedOrders'] : []; 
                $checkedAttribute='';

                if (!empty($idChecked)){

                $isChecked = in_array($numero_commande, $idChecked);
                $checkedAttribute = $isChecked ? 'checked' : '';

                }

                // Utilisez l'opérateur ternaire pour ajouter l'attribut "checked" si la commande est cochée


                echo '<tr>
                        <td>'.$numero_commande.'</td>
                        <td>'.$date_commande.'</td>
                        <td>'.$statut_commande.'</td>
                        <td> <input class="form-check-input" type="checkbox" value="' . $commande_id . '" name="selectedOrders[]" ' . $checkedAttribute . '></td>
                    </tr>';

            }

        }
        echo '</table>';
        self::Template_button($tableDisplay);
        echo '</div>';

        echo '</table>';
        echo '</form>';       
    }

    static public function Template_header_table($tableDisplay){
        echo '<table class="table table-striped" id="commandeTable" style="display: ' . $tableDisplay . ';">
        <thead>
            <tr>
                <th class="manage-column">numero commande</th>
                <th class="manage-column">date commande</th>
                <th class="manage-column">statut commande</th>
                <th>selection</th>
            </tr>
        </thead>';
    }

    static public function Template_select($selectedDate){
        echo '<select class="form-select form-select-sm" aria-label=".form-select-sm" id="dateSelect">';
        echo '<option value="0" ' . ($selectedDate === '0' ? 'selected' : '') . '>Choisir le délai d\'expédition</option>';
        for ($i = 1; $i <= 5; $i++) {
            echo '<option value="' . $i . '" ' . ($selectedDate === strval($i) ? 'selected' : '') . '>J+' . $i . '</option>';
        }
        echo '</select>';
        echo ' <input type="hidden" id="selectedDate" name="selectedDate" value="'.$selectedDate.'">';   
    }

    static public function Template_button($tableDisplay){
        echo '<table class="table table-striped" id="commandeTable" style="display: ' . $tableDisplay . ';">
            <tr>
                <td>';
                echo ' <input type="hidden" id="export" name="export" value="colis">';   
                echo '<input type="submit" id="exportfile" class="page-title-action" value="Export Colis">';


                echo'    <a class="page-title-action" href="?page=fand-shipping-export-plugin&export=colis&selectedOrders=selectedOrders[]">Export Colis</a>
                </td>
                <td style="text-align:right">
                    <a class="page-title-action" href="?page=fand-shipping-export-plugin&export=cond">Export Conditionnement</a>
                </td>
            </tr>';
        echo '</table>';

    }

}

