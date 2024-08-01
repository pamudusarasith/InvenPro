<?php
    define('APP_PATH', realpath($_SERVER["DOCUMENT_ROOT"] . "/../app"));

    spl_autoload_register(function ($class) {
        $parts = explode('\\', $class);

        if (count($parts) == 0 || $parts[0] != 'App')
            return;

        $path = array_slice($parts, 1, -1);
        $className = $parts[array_key_last($parts)];

        $includePath = APP_PATH . "/";
        if (count($path) != 0)
            $includePath = $includePath . strtolower(implode('/', $path)) . "/";
        $includePath = $includePath . $className . ".php";
        
        if (is_file($includePath))
            include_once $includePath;
    });

    try {
        $router = new App\Router();
        $router->dispatch();
    } catch (\Exception $e) {
        App\View::render("errors/500");
        error_log($e->getMessage());
    }
?>