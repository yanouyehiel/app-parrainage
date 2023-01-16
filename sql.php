<?php

$GLOBALS['HOST'] = "localhost";
$GLOBALS['USER'] = "root";
$GLOBALS['PASSWORD'] = "";

$con = NULL;

/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/


function Connect(&$con, $database)
{
  try
  {
    $con = new PDO('mysql:host='. $GLOBALS['HOST'] .";dbname=". $database, $GLOBALS['USER'], $GLOBALS['PASSWORD']);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
  }
  catch(PDOException $e)
  {
    die("Échec de la connexion : " . $e->getMessage());
  }
}



/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/




// Convertit un tableau de chaînes en une chaîne unique dont les elements sont separes par des virgules

function toSqlValues($array)
{
    for($i = 0; $i < count($array); $i++)
        if(!is_numeric($array[$i]))
            $array[$i] = '"'. $array[$i] .'"';
    $string = join(', ', $array);
    return $string;
}

function getLabels($nbLabels)
{
  $str = "?";
  for ($i = 2; $i <= $nbLabels; $i++)
    $str .= ", ?";
  //$str .= ":param$i";
  return $str;
}



/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/




// Requete "insert into..."

function sqlAdd($table, $fields, $values)
{
    global $con;

    $tab = explode(",", $values);
    try
    {
      $count = count($tab);
      $labels = getLabels($count);
      $sql = 'INSERT INTO '. $table .'('. $fields .') VALUES ('. $labels .')';
      $stmt = $con->prepare($sql);
      for ($i = 0; $i < $count; $i++)
      {
        $stmt->bindParam($i+1, $tab[$i]);
      }
      $stmt->execute();
    }
    catch(PDOException $e)
    {
      echo "<br />Echec de la requete : " . $e->getMessage() . "<br />";
    }
}



/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/



// Requete "delete from..."

function sqlDelete($table, $cond)
{
  global $con;

  //si le parametre $cond (condition) est une chaine vide on considere que cela vaut un "true"    
  if ($cond === "")
      $cond = "1";
  try
  {
    $stmt = $con->prepare('DELETE FROM '.$table.' WHERE '.$cond);
    $stmt->execute();
  }
  catch(PDOException $e)
  {
    echo "<br />Echec de la requete : " . $e->getMessage() . "<br />";
  }
}



/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/



// Requete "update..."

function sqlUpdate($table, $update, $cond)
{
  global $con;

  //si le parametre $cond (condition) est une chaine vide on considere que cela vaut un "true"    
  if ($cond === "")
      $cond = "1";
  try
  {
    $stmt = $con->prepare('UPDATE '.$table.' SET '.$update.' WHERE '.$cond);
    $stmt->execute();
  }
  catch(PDOException $e)
  {
    echo "<br />Echec de la requete : " . $e->getMessage() . "<br />";
  }
}



/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/




function SQLInsertFromCSV ($servername, $dbname, $user, $password, $source_file, $table, $columns, $nb_columns)
{
  $fp = fopen($source_file, "r");
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $user, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
  $tab = null;
  $i = 0;
  
  // si ce parametre n'est pas vide alors on entoure de parantheses 
  if (!empty($columns))
  $columns = '(' . $columns . ')';
  
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

      $i = 0;
      $insert = "INSERT INTO `$table` $columns VALUES (";
      $values = "";
  
      // tableau de substitution
      $cpy = [];
  
      for (;  $i < $nb_columns; $i++)
      {
        $tab[$i] = trim($tab[$i]);
        if(is_numeric($tab[$i]))
        {
          array_push($cpy, (int) $tab[$i]);
          $values .= $cpy[$i] . ', ';
        }
        else
        {
          array_push($cpy, $tab[$i]);
          $values .= '"' . $cpy[$i] . '", ';
        }
      }

      // on retire le dernier ", " en trop
  
      $values = substr($values, 0, -2); 
  
      // finalisation le string de la commande
      
      $insert .= $values . ')';
  
      // echo "<br />res = $insert <br />";
      // echo var_dump($cpy) . "<br />";
  
      $stmt = $pdo->prepare($insert);
      $stmt->execute();
    }
  }
  
  fclose($fp);

  $pdo = null;
}
?>