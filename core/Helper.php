<?php

namespace App\core;

class Helper
{
  /**
   * Will filter strings from unwanted characters
   *
   * @param mixed $value
   * @return string
   */
  final public function filter(string $value)
  {
    return trim(mb_convert_encoding(htmlspecialchars(html_entity_decode($value)), "utf-8"));
  }

  /**
   * Determine whether an array is nested
   *
   * @param array $array
   * @return boolean
   */
  public function isNestedArray(mixed $array)
  {
    foreach ($array as $element) {
      if (is_array($element)) {
        return true; // Nested array found
      } elseif (is_object($element) && !is_array($element)) {
        // Check for nested arrays within objects
        return $this->isNestedArray($element);
      }
    }

    return false; // No nested arrays found
  }
}
