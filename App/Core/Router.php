<?php

namespace App\Core;

use App\Core\View;

class Router
{
  private mixed $routes = [];
  private array $params = [];

  public function __construct()
  {
    $in = APP_PATH . "/Core/Routes.php";
    $this->routes = include_once $in;
  }

  private function matchRoute(string $url, string $method): ?array
  {
    // First try exact match
    if (isset($this->routes[$method][$url])) {
      return ['route' => $this->routes[$method][$url], 'params' => []];
    }

    // Try matching dynamic routes
    foreach ($this->routes[$method] as $route => $handler) {
      $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $route);
      $pattern = "@^" . $pattern . "$@D";

      if (preg_match($pattern, $url, $matches)) {
        // Extract named parameters
        $params = array_filter($matches, function ($key) {
          return !is_numeric($key);
        }, ARRAY_FILTER_USE_KEY);

        return ['route' => $handler, 'params' => $params];
      }
    }

    return null;
  }

  public function dispatch(): void
  {
    $url = explode("?", $_SERVER["REQUEST_URI"])[0];
    $method = $_SERVER['REQUEST_METHOD'];

    $match = $this->matchRoute($url, $method);

    if (!$match) {
      error_log("Route '$method $url' not found");
      View::redirect("/404.html");
      return;
    }

    $this->params = $match['params'];
    [$controller, $action] = explode("::", $match['route']);

    if (!$controller || !class_exists($controller)) {
      error_log("Controller $controller not found");
      View::redirect("/500.html");
      return;
    }

    $controllerObj = new $controller();

    if (!method_exists($controllerObj, $action)) {
      error_log("Action $action not found in $controller");
      View::redirect("/500.html");
      return;
    }

    if (!$this->params) {
      $controllerObj->$action();
    } else {
      $controllerObj->$action($this->params);
    }
  }
}
