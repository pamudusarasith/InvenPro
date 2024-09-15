<?php

namespace App;

use Exception;

class Utils
{
    /**
     * @throws Exception
     */
    public static function loadDotEnv()
    {
        $in = ROOT_PATH . "/.env";
        if (!is_file($in)) {
            throw new Exception("No .env file found.");
        }
        $lines = file($in, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), "#") === 0) {
                continue;
            }
            [$key, $value] = explode("=", $line);
            $_ENV[$key] = $value;
        }
    }
}
