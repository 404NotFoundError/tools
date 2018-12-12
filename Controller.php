<?php

namespace Tool;


/**
 * Mother of Controllers.
 */
class Controller
{

  /**
   * List of parameters
   * @var array, $params
   */
  protected $params = [];

  /**
   * List of errors
   * @var array, $errors
   */
  protected $errors = [];

  /**
   * Add new errors in error list
   * @var string $key, Error name
   * @var string $message, Error description
   */
  protected function setErrors(string $key, string $value)
  {
    return $this->errors[$key] = $value;
  }

  /**
   * @var array\Request-pamaeters
   */
  public function setParams(array $params)
  {
    foreach ($params as $key => $param) {
      $params[$key] = filter_var($param, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    return $this->params = $params;
  }

  /**
   * Render new twig view.
   * @param string $template, The template we want load.
   * @param array $parameters, The parameters.
   */
  protected function view(string $template, array $parameters)
  {
      // On ajoute la liste des erreurs
      array_push($parameters, $this->errors);
      // Load twig classes.
      require_once '../vendor/twig/twig/lib/Twig/Loader/Filesystem.php';
      require_once '../vendor/twig/twig/lib/Twig/Environment.php';
      // Default path of templates
      $templates_path =   __dir__ . '/../templates/';
      $loader = new \Twig_Loader_Filesystem($templates_path);
      $twig = new \Twig_Environment($loader, array(
        'cache' => false, // or path to cache folder.
      ));
      // Render
      echo $twig->render($template, $parameters);
  }



}
