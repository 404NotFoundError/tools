<?php

namespace Config;

use PDO;

class DataBase
{

  private $conn;

  private $table;

  private $fields = [];

  public function __construct()
  {
      try {
         $this->conn = new PDO('mysql:host=localhost;dbname=adebayo', 'root', 'root');
      } catch (PDOException $e) {
         echo "<strong> Problème de connexion à la base de donnée </strong>";
         print "Erreur !: " . $e->getMessage() . "<br/><br/>";
         die();
      }
  }


  public function update(object $entity)
  {
    // Get data on object
    $this->getTable($entity);
    $this->getFields($entity);


  }

  /**
   *
   */
  public function insert(object $entity)
  {
     // Get data on object
     $this->getTable($entity);
     $this->getFields($entity);

     //
     $fieldsName = implode(',', array_keys($this->fields));
     $values = [];
     foreach ($this->fields as $key => $value) {
       array_push($values, ':'. $key);
     }
     $values = implode(', ', $values);

     // Préparation de la requêtes.
     $request = $this->conn->prepare(
       "INSERT INTO ". $this->table ." (". $fieldsName .")" . " VALUES " . "(" . $values .")"
     );

     // On bind les paramètres
     foreach ($this->fields as $key => $value) {
       $request->bindValue(':' . $key, $value, PDO::PARAM_STR);
     }

     return $request->execute();

  }
  
  private function getFields(object $entity)
  {
     return $this->fields = get_object_vars($entity);
  }

  private function getTable(object $entity)
  {
    $entity = get_class($entity);
    $parts  = explode('\\', $entity);
    $table  = strtolower(end($parts));
    $table  = str_replace($table, $table . 's', $table);
    return $this->table = $table;
  }




}
