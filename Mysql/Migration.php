<?php

namespace Tool\Mysql;

use Tool\Mysql\Mysql;

/**
 *
 */
class Migration
{

  public static function connect()
  {
     require_once __dir__ . '/../../config.php';
     Mysql::connect();
  }

  /**
   * Renvoie la liste des entitées
   * @return array, $entityList
   */
  private static function getEntities()
  {
    // On réccupère la lites des entités
    $entities = glob( __dir__ . '/../../src/Entity/*php');
    foreach ($entities as $key => $value) {
      $value = explode('/', $value);
      $entity = str_replace('.php', '', end($value));
      $entityList[] = $entity;
    }

    return $entityList;

  }

  public static function makeMigration()
  {
     echo "hello word";

  }




}
