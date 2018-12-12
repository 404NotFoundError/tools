<?php

namespace Tool\Route;

/**
 * RouteLooader class
 */
class RouteLoader
{

  /**
   * @param array, $routes
   */
  public function __construct(array $routes)
  {
     define('APP_ROUTES', $routes);
     $method = $_SERVER['REQUEST_METHOD'];
     foreach ($routes[$method] as $key => $route) {
       /* On va vérifier s'il des paramètres dans la route et le reformater à l'aide de l'url couante si c'est le cas */
       $data = $this->formatRequestIfParamsExist($route['path']);
       /* Si on trouve une route on charge le controlleur et sort de la boucle */
       if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] === $data['route'] || $_SERVER['REQUEST_URI'] === $data['route']) {
         define('APP_ROUTE', $route);
         /* On regarde si la route à de smiddlewares, si oui on les gère */
         if (isset($route['middlewares'])) $this->loadMiddlewares($route['middlewares']);
         /* On charge le controller et l'action et les paramètres */
         $controller = 'App\Controller\\' .  $route['controller'];
         $action = $route['action'];
         $controller = new $controller;
         $controller->setParams($data['params']);
         $controller->$action();
         exit();
       }

     }

     // Dans le cas ou aucune route n'a été trouver on retourne une 404
      if (APP_MODE === 'dev') {
        die('Aucune route n\' a été trouvé pour: <strong>' . $_SERVER['REQUEST_URI'] . '</strong>');
      }else {
        return http_response_code(404);
        exit();
      }

  }

  // TODO:
  // Vérifie si un controller existe
  private function verifyIfControllerExist()
  {

  }

  // TODO:
  // Vérifie si une route existe
  private function verifyIfMethodExist()
  {

  }

  /**
   * Charge les middlewares défines dans la route
   * @param array, $middlewares
   */
  private function loadMiddlewares(array $middlewares)
  {
     foreach ($middlewares as $key => $middleware) {
       /* On vérifie si le middleware à été définis bien si on est en mode dev */
       if (APP_MODE === 'dev') {
         !in_array($middleware, array_keys(APP_MIDDLEWARES)) ? die('Le middleware <strong>' . $middleware . '</strong> definit dans la route <strong>' . APP_ROUTE['path'] . '</strong> n\'est pas définit dans la liste des middlewares.') : true;
       }
       /* On charge les middleware */
       // TODO: Charger les middleware
       $middleware = 'App\Middleware\\' .  APP_MIDDLEWARES[$middleware];
       $middleware = explode('@', $middleware);
       /* On istencie la classe */
       $class = new $middleware[0];
       /* On charge une action qi elle ecxiste */
       if (isset($middleware[1])) {
          $method = $middleware[1];
          $class->$method();
       }

     }
  }

  /**
   *
   * @return array, $data
   */
  public function formatRequestIfParamsExist(string $path)
  {
      $url   = explode('/', $_SERVER['REQUEST_URI']);
      $route = explode('/', $path);
      // On vérifie s'il y a des paramètre dans la route
      foreach ($route as $key => $row) {
        // On vérifie si le nombre de '/' équivaut et qu'il existe des paramètres dans la rout
        if (count($url) === count($route) && preg_match('%^{(.)*}$%', $row)) {
          /* On a des des paramètres dans la route on remplace les paramètres pas l'ofset
             correspondant à la requête courante */
          $variable = str_replace(['{','}'], '', $row);
          $route[$key] = $url[$key];
          $data['params'][$variable] = $url[$key];
          $data['route'] = implode('/', $route);
        }else {
          $data['params'] = [];
          $data['route']  = $path;
        }

      }

      return $data;

  }





}
