<?php

namespace App\Core;

class View
{
  /**
   * Renders a view file.
   *
   * @param string $view The name of the view file (without extension).
   * @param array $data An associative array of variables to pass to the view.
   * @return void
   */
  public static function render(string $view, array $data = []): void
  {
    $content = APP_PATH . "/Views/$view.php";
    if (is_readable($content)) {
      extract($data);
      require $content;
    } else {
      error_log("View not found: $view");
      self::redirect("/500.html");
    }
  }

  /**
   * Renders a template file.
   *
   * @param string $view The name of the view file (without extension).
   * @param array $data An associative array of variables to pass to the view.
   * @return void
   */
  public static function renderTemplate(string $view, array $data = []): void
  {
    $data["view"] = $view;
    self::render("Template", $data);
  }

  /**
   * Redirects the user to a new URL.
   *
   * @param string $url The URL to redirect to.
   * @return void
   */
  public static function redirect(string $url): void
  {
    header("Location: $url");
    exit;
  }
}
