<?php

namespace Tool\Mysql;

use PDO;
use Tool\Mysql\Mysql;


/**
 * MysqlEntityManager Trait
 * by Adebayo HOUNTONDJI
 */
trait MysqlEntityManager
{

    /**
     * Le nom de la table courante (db)
     * @var string, $table
     */
    private $table;

    /**
     * Le nom de la classe de courante (entity)
     * @var string, $class
     */
    private $class;

    /**
     * La condition pour la requete
     * @var string, $condition
     */
    private $condition;

    /**
     * La liste des champs et valuers de l'entité courante
     * @var array, $values
     */
    private $values;

    /**
     * Retourne le nom de table correspondant une classe d'entité
     * @return string\MysqlEntityManager
     */
    public function getTable(string $class)
    {

      class_exists($class) ? $this->class = $class : die('La classe <strong>' . $class . '</strong> n\'existe pas');

      $parts = explode('\\', $class);
      $table = strtolower(end($parts));
      $table = str_replace($table, $table . 's', $table);
      $this->table = $table;

      return $this;

    }

    /**
     * Insertion d'une entité
     * @param object, $entity
     * @return bool,
     */
    public function create(object $entity)
    {

      // Les propriétes des l'entité
      $properties = $entity->getProperties();
      // Les valeurs assignées aux propriétés
      $values = $entity->getValues();
      // Les champs dans la table : query string
      $fields = implode(', ', $properties);
      // Les marqueurs nommée en tableau
      foreach ($properties as $key => $value) {
        $markers[] = ':' . $value;
      }
      // Les marqueurs nommés en string
      $markers = implode(', ', $markers);
      // Connexion à Mysql
      $conn = Mysql::connect();
      // Préparation de la requêtes
      $request = $conn->prepare("INSERT INTO $this->table ($fields) VALUES ($markers)");
      // Filtrage des valeurs
      foreach ($values as $key => $value) {
        $request->bindValue(':' . $key , $value, PDO::PARAM_STR);
      }
      // Execution de la requête
      return $request->execute();

    }

    /**
     *
     * @param array, $condition, La liste des conditions
     */
    public function search(array $condition)
    {

       foreach ($condition as $key => $value) {
         if (!isset($value[2])) { $value[2] = ''; }
         $condition_list[] = $key . ' ' . $value[0] . ' :' .  $key. ' ' . strtoupper($value[2]);
         $this->values[$key] = $value[1];
       }

       $this->condition = implode(' ', $condition_list);

       return $this;
    }

    /**
     * Lecture de l'ensemble du contenu d'une table
     * @param array, $entity
     */
    public function getAll()
    {
       $conn = Mysql::connect();
       $request = $conn->prepare("SELECT * FROM $this->table");
       $request->execute();

       return $request->fetchAll(PDO::FETCH_CLASS, $this->class);
    }

    /**
     * Lecture de plusieur ligne de table
     * @return array\Entity-List
     */
    public function get()
    {
      // Connexion à la base de donnée
      $conn = Mysql::connect();
      // Préparation de la requete
      $request = $conn->prepare("SELECT * FROM $this->table WHERE $this->condition");
      // Bind
      foreach ($this->values as $key => $value) {
        $request->bindValue(':' . $key , $value, PDO::PARAM_STR);
      }
      // Exécution de la requete
      $request->execute();
      // On retourne un tableau contenant en objet la liste de réponse
      return $request->fetchAll(PDO::FETCH_CLASS, $this->class);

    }

    /**
     * Lecture d'une ligne de table
     * @return object/bool
     */
    public function getOne(int $id = null)
    {
      // Connexion à la base de donnée
      $conn = Mysql::connect();

      if (isset($id) && is_integer($id)) {
        // Préparation de la requête
        $request = $conn->prepare("SELECT * FROM $this->table WHERE id = :id");
        // Bind
        $request->bindValue(':id', $id, PDO::PARAM_INT);
        // Execution de la requête
        $request->execute();
        // On retourne
        return $request->fetchObject($this->class);

      }else {

        // Préparation de la requete
        $request = $conn->prepare("SELECT * FROM $this->table WHERE $this->condition");
        // Bind
        foreach ($this->values as $key => $value) {
          $request->bindValue(':' . $key , $value, PDO::PARAM_STR);
        }
        // Exécution de la requete
        $request->execute();
        // Fetch en objet
        return $request->fetchObject($this->class);

      }


    }

    /**
     * Mise à jour d'une entité
     * @return object
     */
    public function update(object $entity)
    {

       $conn = Mysql::connect();

       $properties = $entity->getProperties();

       $values = $entity->getValues();

       foreach ($properties as $key => $property) {
         $dataToSet[] = $property. ' = :' . $property;
       }

       $dataToSet = implode(', ', $dataToSet);

       $id = $entity->getId();

       $request = $conn->prepare("UPDATE $this->table SET $dataToSet WHERE id = $id ");

       foreach ($values as $key => $value) {
         $request->bindValue(':' . $key , $value, PDO::PARAM_STR);
       }

       return $request->execute();

    }

    /**
     * Supprime une ligne
     *
     */
    public function delete(object $entity)
    {
       // Connexion à Mysql
       $conn = Mysql::connect();
       // Préparation de la requete
       $request = $conn->prepare("DELETE FROM $this->table WHERE id = :id ");
       // Bind
       $request->bindValue(':id', $entity->getId() , PDO::PARAM_INT);
       // Execution de la requete
       return $request->execute();

    }

    /**
     * @param string $table, le nom de la table
     * @param string $fields, le nom du champs
     */
    private function mysqlRequestControl(string $table, array $fields)
    {
        // On récupère la liste des tables
        $tables = Mysql::getTables();
        if (in_array($table, $tables)) {
          // On réccupère la liste des chmaps
          $tablefields = Mysql::getTableFields($table);
          // Pour chaque champs on récupère le nom des champs
          foreach ($tablefields as $key => $tablefield) {
            $tableFieldName[] = $tablefield['Field'];
          }

          app_debug(array_diff($fields, $tableFieldName));

        }else {
          die('La table n\'existe <strong>' . $table . '</strong> pas');
        }

    }




}
