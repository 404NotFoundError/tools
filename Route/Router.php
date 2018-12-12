<?php

namespace Tool\Route;

use Exception;

/**
 * Router Class
 */
class Router
{

  /**
   * List of routes
   * @var array, $routes
   */
  private $routes = [
    'GET'    => [],
    'POST'   => [],
    'PUT'    => [],
    'DELETE' => []
  ];

  /**
   *
   * @var string, $method
   */
  private $method;

  /**
   *
   * @var int, $offset
   */
  private $offset;

  /**
   * Create new route
   * @var string, $method
   * @var string, $path
   * @var string, $action
   * @return Router
   */
  public function create(string $method, string $path, string $action)
  {
    /** save path in route list **/
    $this->save($method, $path);
    /** recover controller and action **/
    $this->getControllerAndAction($action);

    return $this;
  }

  /**
   * Save a path a list route id path does not exist
   * @var string, $method
   * @var string, $newRoute
   */
  private function save($method, $newRoute)
  {
    /* Si on est en mode dev on vérifie si la route n'existe pas déjà */
    if (APP_MODE === 'dev' || APP_MODE === 'development') {
      foreach ($this->routes[$method] as $key => $value) {
        if ($value['path'] === $newRoute) die('The route  <strong>"' . $newRoute . '"</strong> already exist');
      }
    };

    /* Si le chemin n'existe pas dans la liste des routes */
    $index = count($this->routes[$method]);
    $this->routes[$method][$index]['path'] = $newRoute;
    /* Le chemin de la route actuel est */
    $this->method = $method;
    $this->offset = $index;

    /* On retorune l'instance */
    return $this;

  }

  /**
   * Definition du controller et de la méthode pour une route
   * @var string, $action
   */
  private function getControllerAndAction(string $action)
  {
     $data = explode('@', $action);
     if (isset($data[1])) {
       $this->routes[$this->method][$this->offset]['controller'] = $data[0];
       $this->routes[$this->method][$this->offset]['action'] = $data[1];
     }else {
       die('<strong> "' . $action . '" </strong> is not a correct format, Try like this <strong> controllerName@methodName <strong>.');
     }
  }

  /**
   * Nommé une route
   * @param string $name
   * @return Router
   */
  public function name(string $name)
  {
    $this->routes[$this->method][$this->offset]['name'] = $name;
    return $this;
  }

  /**
   * Décrire le contenue d'une route
   * @param string $name
   * @return Router
   */
  public function description(string $description)
  {
    $this->routes[$this->method][$this->offset]['description'] = $description;
    return $this;
  }

  /**
   * Définitipn de la liste des middleware à utiliser
   * @param array, $middlewares
   * @return Router
   */
  public function middleware(array $middlewares)
  {
    $this->routes[$this->method][$this->offset]['middlewares'] = $middlewares;
    return $this;
  }

  /**
   * Retourne la liste des routes
   * @return Router
   */
  public function getRoutes()
  {
    return $this->routes;
  }



}
