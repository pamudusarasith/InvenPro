<?php

namespace App\Core;

class Utils
{
  /**
   * Parse .env file and set environment variables
   * @param string $path Path to .env file
   * @return void
   */
  public static function loadEnv($path = null)
  {
    if ($path === null) {
      $path = dirname(__DIR__, 2) . '/.env';
    }

    if (!file_exists($path)) {
      error_log('.env file not found');
      View::redirect('/500.html');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
      // Skip comments
      if (strpos(trim($line), '#') === 0) {
        continue;
      }

      // Split line by first equals sign
      $parts = explode('=', $line, 2);

      if (count($parts) === 2) {
        $key = trim($parts[0]);
        $value = trim($parts[1]);

        // Remove quotes if present
        $value = trim($value, '"\'');

        // Set environment variable
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
      }
    }
  }
}
