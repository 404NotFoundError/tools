<?php

// Test la connection à la base de données selon la configuration renseigner
function app_test_database()
{
    $infos = [DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT, DB_CHARSET];

    foreach ($infos as $key => $value) {
      if (empty($value)) die('Vous avez <strong> des constantes non définis </strong> au niveau de la configuarion de votre base de donnée (voir le ficher <strong> config.php </strong>)');
    }

    try {

      $con = new PDO('mysql:host=' . DB_HOST .';dbname=' . DB_NAME, DB_USER , DB_PASS);
      // TODO: Il faut qu'elle renvoie les tables etc ...
      $response =  [
        'message' => 'Application connectée avec succès à la base de donnée <strong>' . DB_NAME . '</strong>.',
        'type'    => $con,
        'tables'  => []
      ];

      app_debug($response);

    } catch (PDOException $e) {
       echo "<strong> Problème de connexion à la base de donnée </strong>";
       print "Erreur !: " . $e->getMessage() . "<br/><br/>";
       die();
    }

}

function app_database_create()
{

}

function app_database_create_table()
{

}
