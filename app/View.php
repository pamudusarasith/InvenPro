<?php

namespace App;

class View
{
    /**
     * Renders a view file.
     *
     * @param string $view The name of the view file (without extension).
     * @param array $data An associative array of variables to pass to the view.
     * @return void
     */
    static function render(string $view, array $data = []): void
    {
        $content = APP_PATH . "/views/$view.view.php";
        if (is_readable($content)) {
            extract($data);
            require $content;
        } else {
            self::render('errors/404');
        }
    }
}
