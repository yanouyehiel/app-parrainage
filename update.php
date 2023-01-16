<?php
include_once("./sql.php");

function SQLUpdateFromCSV (&$con, $source_file, $stmtTemplate)
{

  $fp = fopen($source_file, "r");
  $tab = null;
  
  $loop = true;

  while($loop || !feof($fp))
  {
    $tab = fgetcsv($fp);

    // si on rencontre une ligne vide on saute le traitement de la ligne
    if (empty($tab) || !is_array($tab))
      $loop = false;
    else
    {
      foreach($tab as $el)
      {
        if($el == null)
        {
          $loop = false;
          break;
        }
      }
  
      if ($loop === false)
      {
        continue;
      }
      
      // On compte le nombre de paramètres de la requête qui sont représentés par le symbole '?'
      $nbOccur = substr_count($stmtTemplate, "?");

      // Déclaration du tableau $tmp pour contenir les différentes valeurs à donner aux '?' pour compléter la requête 
      $temp = [];
      
      $stmt = $con->prepare($stmtTemplate);
      
      // Depuis les informations du fichier csv on donne des valeurs aux différents paramètres '?'
      for ($i=0; $i < $nbOccur; $i++)
      {
        array_push($temp, trim($tab[$i]));
      }

      // execute(array()) où array() = $temp
      $stmt->execute($temp);
      
      //print_r($tab);    
      
    }
  }
  
  fclose($fp);

}

Connect($con, "parrainage-2022");
// Exemple d'appel de la fonction SQLUpdateFromCSV
SQLUpdateFromCSV($con, "./etudiants.csv", "UPDATE etudiants SET filiere =: ?, niveau =: ? WHERE matricule =: ?");

?>