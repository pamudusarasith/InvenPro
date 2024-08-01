<?php

namespace App;

class Router {
    private $routes = array();

    public function __construct() {
        $in = APP_PATH . "/Routes.php";
        if (!is_file($in)) {
            throw new \Exception("No routes defined.");
        }
        $this->routes = include $in;
    }

    function dispatch(): void {

        $url = $_SERVER["REDIRECT_URL"];
        if (!array_key_exists($url, $this->routes)) {
            View::render("errors/404");
            return;
        }
        
        $controller = $this->routes[$url];
        if (class_exists($controller)) {
            $controllerObj = new $controller();
            $controllerObj->index();
        } else {
            throw new \Exception("Controller $controller not found.");
        }
    }
}

?>