<?php
    function lire_csv($nom_fichier, $separateur =";"){
        $row = 0;
        $donnee = array();    
        $f = fopen ($nom_fichier,"r");
        $taille = filesize($nom_fichier)+1;
        while ($donnee = fgetcsv($f, $taille, $separateur)) {
            $result[$row] = $donnee;
            $row++;
        }
        fclose ($f);
        return $result;
    }

    function requete_insert($donnees_csv, $table){
        $insert = array();
        $i = 0;      
        while (list($key, $val) = @each($donnees_csv)){
            /*On ajoute une valeur vide ' ' en début pour le champs d'auto-incrémentation  s'il existe, sinon enlever cette valeur*/
            if ($i>0){
                $insert[$i] = "INSERT into ".$table." VALUES(' ',".$id_user.",'";     
                $insert[$i] .= implode("','", $val);
                $insert[$i] .= "')";                      
            }$i++;
        }       
        return $insert;
    }

    $fichier= "nom_de_mon_fichier.csv";
    $nom_table = "nom_de_ma_table_sql";
    $donnees = lire_csv($fichier);
    $requetes= requete_insert($donnees, "$nom_table");
    foreach($requetes as $requete)
    {
        $result = mysql_query($requete) or die('Erreur SQL !'. $requete.'<br />'.mysql_error());
    }
?>