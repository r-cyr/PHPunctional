<?php

namespace P;

class Boolean {
  public static $implementations = [];

  private function __construct() {}

  private function __clone() {}

  public static function __callStatic($name, array $arguments)
  {
    if (!isset(self::$implementations[$name])) {
      throw new \Exception("Function $name not found");
    }

    return \call_user_func_array(self::$implementations[$name], $arguments);
  }
}

/**
 * Returns the negation of $a
 *
 * @param bool $a
 * @return bool
 */
Boolean::$implementations['not'] = function($a) {
  return !$a;
};

/**
 * Returns the logical AND of $a and $b
 *
 * @param bool $a
 * @param bool $b
 * @return bool
 */
Boolean::$implementations['and_'] = function($a, $b) {
  return $a && $b;
};
Boolean::$implementations['and_'] = Core::curry(Boolean::$implementations['and_']);

/**
 * Returns the logical OR of $a and $b
 *
 * @param bool $a
 * @param bool $b
 * @return bool
 */
Boolean::$implementations['or_'] = function($a, $b) {
  return $a || $b;
};
Boolean::$implementations['or_'] = Core::curry(Boolean::$implementations['or_']);
