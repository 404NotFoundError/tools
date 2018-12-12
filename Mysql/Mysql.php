<?php

namespace Tool\Mysql;

use Helper\Downloading\Csv;

/**
 *
 * Mysql Class
 */
class Mysql {

  public static function connect()
  {
    try {
      return new \PDO('mysql:host=' . DB_HOST .';dbname=' . DB_NAME, DB_USER , DB_PASS);
    } catch (\PDOException $e) {
       echo "<pre>";
       print_r([
         'status' => '<strong> Problème de connexion à la base de donnée </strong>',
         'error'  => "Erreur !: " . $e->getMessage() . "<br/><br/>"
       ]);
       echo "</pre>";
       die();
    }
  }

  // Renvoie la liste des champs d'un tableau
  public static function getTables()
  {
     $conn = Mysql::connect();
     $tables = $conn->query('SHOW tables')
                    ->fetchAll(\PDO::FETCH_ASSOC);

     // On boucle sur les table pour reccuper unique leur nom.
     foreach ($tables as $key => $table) { $Tables[] = $table['Tables_in_' . DB_NAME];}

     return $Tables;
  }

  // Renvoie la liste des champs d'une table
  public static function getTableFields(string $table)
  {
     $conn = Mysql::connect();
     $fieds = $conn->query('describe ' . $table)
                   ->fetchAll(\PDO::FETCH_ASSOC);

     return $fieds;
  }

  /**
   * Renvoie une liste des tables de la base de donnée
   * @return Mysql
   */
  public static function getTablesDetail()
  {
     // Connexion à la base de donnée
     $conn = Mysql::connect();
     // On réccupère la liste des tables
     $tables = $conn->query('SHOW tables')
                    ->fetchAll(\PDO::FETCH_ASSOC);
     // On creer les champs qu'on veut renvoyer dans le tableau csv
     $Table_keys = ['Table', 'Name', 'Fields'];

     // On chaque table de la base de donnée
     foreach ($tables as $key => $table) {
       // On réccupère la liste des champs
       $fieds = $conn->query('describe ' . $table['Tables_in_' . DB_NAME])
                     ->fetchAll(\PDO::FETCH_ASSOC);

       // Pour chaque champs de la table courante on réccupère les valeurs dans un index.
       foreach ($fieds as $key => $fied) {

         $fieds[$key]['Table'] = $table['Tables_in_' . DB_NAME];

         $fieldNewOrder[$key] = [

           'Table'   => $table['Tables_in_' . DB_NAME],
           'Field'   => $fied['Field'],
           'Type'    => $fied['Type'],
           'Null'    => $fied['Null'],
           'Key'     => $fied['Key'],
           'Default' => $fied['Default'],
           'Extra'   => $fied['Extra']

         ];

       }

       $csv = new Csv(
         APP_NAME . '-' . DB_NAME,
         array_keys($fieldNewOrder[0]),
         $fieldNewOrder
       );

     }



  }





}
