<?php

use App\Utils;

define('DEBUG', true);
define('ROOT_PATH', realpath(__DIR__ . "/.."));
define('APP_PATH', ROOT_PATH . "/app");

spl_autoload_register(function ($class) {
    $parts = explode('\\', $class);

    if (empty($parts) || $parts[0] != 'App') {
        return;
    }

    $path = array_slice($parts, 1, -1);
    $className = $parts[array_key_last($parts)];

    $includePath = APP_PATH . "/";
    if (count($path) != 0) {
        $includePath = $includePath . strtolower(implode('/', $path)) . "/";
    }
    $includePath = $includePath . $className . ".php";

    if (is_file($includePath)) {
        include_once $includePath;
    }
});

set_exception_handler(function ($e) {
    error_log($e->getMessage());
    if (DEBUG) {
        Utils::error(500, $e->getMessage() . "<br>" . nl2br($e->getTraceAsString()));
    } else {
        Utils::error(500);
    }
});

session_start();

$router = new App\Router();
$router->dispatch();
