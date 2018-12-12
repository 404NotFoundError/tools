<?php

// Permet de deburger proprement une variable
function app_debug($value, bool $die = null)
{
  echo "<pre>";
  print_r($value);
  echo "</pre>";

  if ($die === true) die('<strong> End of debug </strong>');

}

// Renvoie la liste des routes
function app_debug_routes()
{
  echo "<pre>";
  print_r(APP_ROUTES);
  echo "</pre>";
}

// Renvoie les coordonnée de la route actuelle
function app_debug_route()
{
  echo "<pre>";
  print_r(APP_ROUTE);
  echo "</pre>";
}

// Téléchage un ficher csv avec la liste des routes
function app_download_routes()
{
   // On reformate l'ordre dans laquelle les champs sont renseigner 
   $method = ['GET', 'POST', 'PUT', 'DELETE'];
   foreach ($method as $key => $value) {
     foreach (APP_ROUTES[$value] as $key => $route) {
       isset($route['name']) ? $newroute['name'] = $route['name'] : $newroute['name'] = '';
       $newroute['path'] = $route['path'];
       $newroute['controller'] = $route['controller'];
       $newroute['action'] = $route['action'];
       isset($route['description']) ? $newroute['description'] = $route['description'] : $newroute['description'] = '';
       isset($route['middlewares']) ? $newroute['middlewares'] = implode(',', $route['middlewares']) : $newroute['middlewares'] = '';
       $routes[] = $newroute;
     }
   }

   $fieldsTitle = ['name', 'path', 'controller', 'action', 'description' , 'middleware(s)'];

   header('Content-type: text/csv');
   header('Content-Disposition: attachment; filename=' . APP_NAME . '.csv');
   $output = fopen('php://output', 'w');
   fputcsv($output, $fieldsTitle);

   foreach ($routes as $key => $route) {
     fputcsv($output, $route);
   }

   fclose($output);

}

// Rafraichi automatiquement la page en fount du temps renseigner
function app_auto_refresh(int $delay)
{
   header('Refresh: ' . $delay . '; ' . $_SERVER['REQUEST_URI']);
}
