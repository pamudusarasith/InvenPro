<?php

namespace App;

class Router
{
    private $routes = [];

    public function __construct()
    {
        $in = APP_PATH . "/Routes.php";
        if (!is_file($in)) {
            throw new \Exception("No routes defined.");
        }
        $this->routes = include $in;
    }

    public function dispatch(): void
    {
        $url = explode("?", $_SERVER["REQUEST_URI"])[0];
        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset($this->routes[$method][$url])) {
            View::render("errors/404");
            return;
        }

        $route = $this->routes[$method][$url];
        $controller = $route['controller'] ?? null;
        $action = $route['action'] ?? 'index';

        if ($controller && class_exists($controller)) {
            $controllerObj = new $controller();

            if (method_exists($controllerObj, $action)) {
                $controllerObj->$action();
            } else {
                throw new \Exception("Method '$action' not found in controller $controller.");
            }
        } else {
            throw new \Exception("Controller $controller not found.");
        }
    }
}
