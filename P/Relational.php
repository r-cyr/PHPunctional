<?php

namespace P;

class Relational {
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
 * Returns true if the two arguments are strictly equal
 *
 * @param mixed $a
 * @param mixed $b
 * @return bool
 */
Relational::$implementations['equal'] = function($a, $b) {
  return $a === $b;
};
Relational::$implementations['equal'] = Core::curry(Relational::$implementations['equal']);

/**
 * Returns true if the two arguments are strictly unequal
 *
 * @param mixed $a
 * @param mixed $b
 * @return bool
 */
Relational::$implementations['notEqual'] = function($a, $b) {
  return $a !== $b;
};
Relational::$implementations['notEqual'] = Core::curry(Relational::$implementations['notEqual']);

/**
 * Returns true if the $a is greater than $b
 *
 * @param mixed $a
 * @param mixed $b
 * @return bool
 */
Relational::$implementations['greaterThan'] = function($a, $b) {
  return $a > $b;
};
Relational::$implementations['greaterThan'] = Core::curry(Relational::$implementations['greaterThan']);

/**
 * Returns true if the $a is greater or equal to $b
 *
 * @param mixed $a
 * @param mixed $b
 * @return bool
 */
Relational::$implementations['greaterOrEqualTo'] = function($a, $b) {
  return $a >= $b;
};
Relational::$implementations['greaterOrEqualTo'] = Core::curry(Relational::$implementations['greaterOrEqualTo']);

/**
 * Returns true if the $a is less than $b
 *
 * @param mixed $a
 * @param mixed $b
 * @return bool
 */
Relational::$implementations['lessThan'] = function($a, $b) {
  return $a < $b;
};
Relational::$implementations['lessThan'] = Core::curry(Relational::$implementations['lessThan']);

/**
 * Returns true if the $a is less or equal to $b
 *
 * @param mixed $a
 * @param mixed $b
 * @return bool
 */
Relational::$implementations['lessOrEqualTo'] = function($a, $b) {
  return $a <= $b;
};
Relational::$implementations['lessOrEqualTo'] = Core::curry(Relational::$implementations['lessOrEqualTo']);
