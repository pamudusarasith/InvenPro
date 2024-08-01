<?php

namespace App;

class View {
    static function render(string $view): void {
        $content = APP_PATH . "/views/$view.view.php";
        if (is_readable($content)){
            require_once $content;
        } else {
            throw new \Exception("View '$view' not found.");
        }
    }
}

?>