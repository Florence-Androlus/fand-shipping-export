<?php

namespace fdse\templates;


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
                $numero_commande = $commande->get_order_number();
                $date_commande = $commande->get_date_created();
                $statut_commande = $commande->get_status();
                echo '<tr>
                        <td>'.$numero_commande.'</td>
                        <td>'.$date_commande.'</td>
                        <td>'.$statut_commande.'</td>
                        <td> <input class="form-check-input" type="checkbox" value="'.$numero_commande.'" id="Check'.$numero_commande.'" ></td>
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
        echo ' <input type="hidden" id="selectedDate" name="selectedDate" value="">';   
    }

    static public function Template_button($tableDisplay){
        echo '<table class="table table-striped" id="commandeTable" style="display: ' . $tableDisplay . ';">
            <tr>
                <td>
                    <a class="page-title-action" href="?page=fand-shipping-export-plugin&export=colis">Export Colis</a>
                </td>
                <td style="text-align:right">
                    <a class="page-title-action" href="?page=fand-shipping-export-plugin&export=cond">Export Conditionnement</a>
                </td>
            </tr>';
        echo '</table>';
    }
    
    static public function export_file(){
        // Vérifier si le paramètre "export" est défini dans l'URL

        if (isset($_GET['export'])) {
            // Obtenir la valeur du paramètre "export"
            $exportType = $_GET['export'];

            // Définir le chemin du répertoire de destination
                    // Obtenir le répertoire d'uploads de WordPress
            $upload_dir = wp_upload_dir();
            
            // Obtenez le chemin complet du répertoire de destination (ajoutez votre sous-dossier personnalisé)
            $custom_directory = $upload_dir['basedir'] . '/fand-shipping-export/'.$exportType.'/';

            // Assurez-vous que le répertoire de destination existe, sinon créez-le
            if (!file_exists($custom_directory)) {
                mkdir($custom_directory, 0777, true);
            }
            
            // Utiliser un switch pour appeler la méthode appropriée en fonction de la valeur du paramètre
            switch ($exportType) {
                case 'colis':
                    $filename = 'export_colis_' . date('YmdHis') . '.csv'; // Nom du fichier avec heure actuelle
                    self::exportColis($custom_directory . $filename); // Appeler la méthode pour l'export de colis
                    break;
    
                case 'cond':
                    $filename = 'export_conditionnement_' . date('YmdHis') . '.csv'; // Nom du fichier avec heure actuelle
                    self::exportConditionnement($custom_directory . $filename); // Appeler la méthode pour l'export de conditionnement
                    break;
                default:
                    // Gérer le cas où le paramètre "export" n'est pas valide
                    // Vous pouvez afficher un message d'erreur ou rediriger l'utilisateur, par exemple.
                    break;
            }
        }
    }
    
    static private function exportColis($filePath){
        // Code pour l'export de colis
        global $wpdb;
        // Obtenez les identifiants de publication des commandes WooCommerce
        $commande_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'shop_order'");

        if(!$handle=fopen($filePath,'w'))
        {
            die('Ouverture du fichier '.$filePath.' impossible');
        }
        file_put_contents($filePath, 'Contenu du fichier CSV pour les colis');
        $r='';
        $r.= '"numero commande";';
        $r.=  '"date_commande";';
        $r.=  '"statut_commande";';
        $r.="\n";
        
        if (fwrite($handle, $r) === FALSE) {
            var_dump("Impossible d'écrire dans le fichier ") ;
            exit;
        }

        foreach ($commande_ids as $commande_id) {
            $commande = wc_get_order($commande_id);
    
            // Affichez la commande dans le tableau
            // Accédez aux données de la commande
            $numero_commande = $commande->get_order_number();
            $date_commande = $commande->get_date_created();
            $statut_commande = $commande->get_status();

            $r='';
            $r.= '"'.$numero_commande.'";';
            $r.= '"'.$date_commande.'";';
            $r.= '"'.$statut_commande.'";';
            $r.="\n";
            if (fwrite($handle, $r) === FALSE) {
                echo "Impossible d'écrire dans le fichier ";
                exit;
            }
        }  

        fclose($handle); 
        self::downloadLink($filePath);
        exit;
    }

    static private function exportConditionnement($filePath){
        // Code pour l'export de conditionnement
        global $wpdb;
        // Obtenez les identifiants de publication des commandes WooCommerce
        $commande_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'shop_order'");

        if(!$handle=fopen($filePath,'w'))
        {
            die('Ouverture du fichier '.$filePath.' impossible');
        }
        // var_dump($filePath);
        file_put_contents($filePath, 'Contenu du fichier CSV pour les colis');
        $r='';
        $r.= '"numero commande";';
        $r.=  '"date_commande";';
        $r.=  '"statut_commande";';
        $r.="\n";
        
        if (fwrite($handle, $r) === FALSE) {
            var_dump("Impossible d'écrire dans le fichier ") ;
            exit;
        }

        foreach ($commande_ids as $commande_id) {
            $commande = wc_get_order($commande_id);
    
            // Affichez la commande dans le tableau
            // Accédez aux données de la commande
            $numero_commande = $commande->get_order_number();
            $date_commande = $commande->get_date_created();
            $statut_commande = $commande->get_status();

            $r='';
            $r.= '"'.$numero_commande.'";';
            $r.= '"'.$date_commande.'";';
            $r.= '"'.$statut_commande.'";';
            $r.="\n";
            if (fwrite($handle, $r) === FALSE) {
                echo "Impossible d'écrire dans le fichier ";
                exit;
            }
        }  

        fclose($handle); 
        self::downloadLink($filePath);
        exit;
    }

    static private function downloadLink($cheminFichierCSV){
        // Vérifiez si le fichier CSV existe
        if (file_exists($cheminFichierCSV)) {
            // Paramètres pour le téléchargement
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename=' . basename($cheminFichierCSV));
            
            // Lire le fichier CSV et le transmettre au client
            ob_clean();
            flush();
            readfile($cheminFichierCSV);
            exit;
        } else {
            echo 'Le fichier CSV n\'existe pas.';
        }
    }

}

