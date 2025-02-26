<?php
session_start();

use App\Core\{View, Router};

define('ROOT_PATH', realpath(__DIR__ . "/.."));
define('APP_PATH', ROOT_PATH . "/App");

spl_autoload_register(function ($class) {
  if (!str_starts_with($class, 'App')) {
    return;
  }

  $path = str_replace('\\', '/', $class);

  $path = ROOT_PATH . "/" . $path . ".php";

  if (is_readable($path)) {
    require_once $path;
  }
});

set_exception_handler(function ($e) {
  error_log($e->getMessage() . "\n" . $e->getTraceAsString());
  View::renderError(500);
});

$router = new Router();
$router->dispatch();
