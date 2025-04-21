<?php

namespace App\Services\Validation\Utils;

class DataAccessor
{
  /**
   * Get value from an array using dot notation
   *
   * @param mixed $data The data to traverse
   * @param string $path Dot notation path e.g. "user.address.city", "user.*.city"
   * @return mixed Single value for direct paths, associative array of values for wildcard paths
   */
  public static function getValue($data, string $path)
  {
    if (empty($path)) {
      return $data;
    }

    if (str_contains($path, '*')) {
      return self::getWildcardValue($data, $path);
    } else {
      return self::getDirectValue($data, $path);
    }
  }

  /**
   * Get a value from an array using direct notation
   *
   * @param mixed $data The data to traverse
   * @param string $path Direct notation path e.g. "user.address.city"
   * @return mixed The found value or null
   */
  private static function getDirectValue($data, string $path)
  {
    $keys = explode('.', $path);
    $value = $data;

    foreach ($keys as $key) {
      if (is_array($value) && array_key_exists($key, $value)) {
        $value = $value[$key];
      } else {
        return null;
      }
    }

    return $value;
  }

  /**
   * Get values from an array using wildcard notation, preserving original keys
   *
   * @param mixed $data The data to traverse
   * @param string $path Wildcard notation path e.g. "user.*.city"
   * @return array|null Associative array of values matching the wildcard pattern,
   * or null if path doesn't exist or cannot apply wildcard to non-array
   */
  private static function getWildcardValue($data, string $path)
  {
    $keys = explode('.', $path);
    $value = $data;

    // Find the position of the first wildcard
    $wildcardPos = array_search('*', $keys);

    // Process all segments before the wildcard
    for ($i = 0; $i < $wildcardPos; $i++) {
      $key = $keys[$i];
      if (is_array($value) && array_key_exists($key, $value)) {
        $value = $value[$key];
      } else {
        return null; // Path before wildcard doesn't exist
      }
    }

    // We're now at the wildcard position
    if (!is_array($value)) {
      return null; // Can't apply wildcard to non-array
    }

    // Handle the wildcard by keeping the original keys
    $result = [];
    $remainingPath = implode('.', array_slice($keys, $wildcardPos + 1));

    foreach ($value as $index => $item) {
      if (empty($remainingPath)) {
        $result[$index] = $item;
      } else {
        $result[$index] = self::getValue($item, $remainingPath);
      }
    }

    return $result;
  }
}
