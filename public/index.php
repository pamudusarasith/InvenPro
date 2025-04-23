<?php

require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

session_start();

use App\Core\{View, Router};

define('ROOT_PATH', realpath(__DIR__ . "/.."));
define('APP_PATH', ROOT_PATH . "/App");

set_exception_handler(function ($e) {
  error_log($e->getMessage() . "\n" . $e->getTraceAsString());
  View::renderError(500);
});

$router = new Router();
$router->dispatch();
