<?php

namespace App;
use App;
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

    public static function requireAuth(): void
    {
        if (!isset($_SESSION["email"])) {
            header("Location: /");
            exit();
        }
    }

    public static function redirect(string $url): void
    {
        header("Location: $url");
        exit();
    }

    public static function can(string $permission): bool
    {
        $model = new App\Models\User();
        return $model->hasPermission($permission);
    }

    public static function error(int $code, ?string $message = null): void
    {
        if ($message) {
            App\View::render("errors/$code", ['message' => $message]);
        } else {
            App\View::render("errors/$code");
        }
        exit();
    }
}
