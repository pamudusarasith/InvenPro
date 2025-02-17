<?php

namespace App\Core;

abstract class Model
{
  protected static DB $db;

  public function __construct()
  {
    self::$db = DB::getInstance();
  }

  public static function filterFields(array $rows, array $fields): array
  {
    $fields = array_flip($fields);
    return array_map(fn($row) => array_intersect_key($row, $fields), $rows);
  }
}
