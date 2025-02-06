<?php

namespace App\Core;

abstract class Model
{
  protected static DB $db;

  public function __construct()
  {
    self::$db = DB::getInstance();
  }
}
