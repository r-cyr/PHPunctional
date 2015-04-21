<?php

namespace P;

class Arithmetic {
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
 * Arithmetic addition
 *
 * @param mixed $a
 * @param mixed $b
 * @return mixed
 */
Arithmetic::$implementations['add'] = function($a, $b) {
  return $a + $b;
};
Arithmetic::$implementations['add'] = Core::curry(Arithmetic::$implementations['add']);

/**
 * Arithmetic substraction
 *
 * @param mixed $a
 * @param mixed $b
 * @return mixed
 */
Arithmetic::$implementations['substract'] = function($a, $b) {
  return $a - $b;
};
Arithmetic::$implementations['substract'] = Core::curry(Arithmetic::$implementations['substract']);

/**
 * Arithmetic multiplication
 *
 * @param mixed $a
 * @param mixed $b
 * @return mixed
 */
Arithmetic::$implementations['multiply'] = function($a, $b) {
  return $a * $b;
};
Arithmetic::$implementations['multiply'] = Core::curry(Arithmetic::$implementations['multiply']);

/**
 * Arithmetic division
 *
 * @param mixed $a
 * @param mixed $b
 * @return mixed
 */
Arithmetic::$implementations['divide'] = function($a, $b) {
  return $a / $b;
};
Arithmetic::$implementations['divide'] = Core::curry(Arithmetic::$implementations['divide']);

/**
 * Arithmetic modulo (Remainder)
 *
 * @param mixed $a
 * @param mixed $b
 * @return mixed
 */
Arithmetic::$implementations['modulo'] = function($a, $b) {
  return $a % $b;
};
Arithmetic::$implementations['modulo'] = Core::curry(Arithmetic::$implementations['modulo']);

/**
 * Arithmetic exponentiation
 *
 * @param mixed $a
 * @param mixed $b
 * @return mixed
 */
Arithmetic::$implementations['exp'] = function($a, $b) {
  return \pow($a, $b);
};
Arithmetic::$implementations['exp'] = Core::curry(Arithmetic::$implementations['exp']);

/**
 * Returns the successor of a number
 *
 * @param mixed $a
 * @return mixed
 */
Arithmetic::$implementations['succ'] = function($a) {
  return $a + 1;
};

/**
 * Returns the predecessor of a number
 *
 * @param mixed $a
 * @return mixed
 */
Arithmetic::$implementations['pred'] = function($a) {
  return $a - 1;
};

/**
 * Negates a number
 *
 * @param mixed $a
 * @return mixed
 */
Arithmetic::$implementations['negate'] = function($a) {
  return -$a;
};

/**
 * Returns if $a is even
 *
 * @param mixed $a
 * @return boolean
 */
Arithmetic::$implementations['even'] = function($a) {
  return $a % 2 === 0;
};

/**
 * Returns if $a is odd
 *
 * @param mixed $a
 * @return boolean
 */
Arithmetic::$implementations['even'] = function($a) {
  return $a % 2 !== 0;
};
