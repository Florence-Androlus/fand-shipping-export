<?php

namespace fdse\includes;

class Export_File{

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

            // On s'assure que le répertoire de destination existe, sinon créez-le
            if (!file_exists($custom_directory)) {
                mkdir($custom_directory, 0777, true);
            }
            
            // On utilise un switch pour appeler la méthode appropriée en fonction de la valeur du paramètre
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
        // Obtenir les identifiants de publication des commandes WooCommerce
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
        // téléchargement au client
        self::downloadLink($filePath);
        exit;
    }

    static private function exportConditionnement($filePath){
        // Code pour l'export de conditionnement
        global $wpdb;
        // Obtenir les identifiants de publication des commandes WooCommerce
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
        // téléchargement au client
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

