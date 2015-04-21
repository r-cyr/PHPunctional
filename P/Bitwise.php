<?php

namespace P;

class Bitwise {
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
 * Returns the bitwise NOT(~) of $a
 *
 * @param int $a
 * @return bool
 */
Bitwise::$implementations['not'] = function($a) {
  return ~$a;
};

/**
 * Returns the bitwise AND(&) of $a and $b
 *
 * @param int $a
 * @param int $b
 * @return bool
 */
Bitwise::$implementations['and_'] = function($a, $b) {
  return $a & $b;
};
Bitwise::$implementations['and_'] = Core::curry(Bitwise::$implementations['and_']);

/**
 * Returns the bitwise OR(|) of $a and $b
 *
 * @param int $a
 * @param int $b
 * @return bool
 */
Bitwise::$implementations['or_'] = function($a, $b) {
  return $a | $b;
};
Bitwise::$implementations['or_'] = Core::curry(Bitwise::$implementations['or_']);

/**
 * Returns the bitwise XOR(^) of $a and $b
 *
 * @param int $a
 * @param int $b
 * @return bool
 */
Bitwise::$implementations['xor_'] = function($a, $b) {
  return $a ^ $b;
};
Bitwise::$implementations['xor_'] = Core::curry(Bitwise::$implementations['xor_']);

/**
 * Returns $a left shifted by $b positions
 *
 * @param int $a
 * @param int $b
 * @return bool
 */
Bitwise::$implementations['leftShift'] = function($a, $b) {
  return $a << $b;
};
Bitwise::$implementations['leftShift'] = Core::curry(Bitwise::$implementations['leftShift']);

/**
 * Returns $a right shifted by $b positions
 *
 * @param int $a
 * @param int $b
 * @return bool
 */
Bitwise::$implementations['rightShift'] = function($a, $b) {
  return $a >> $b;
};
Bitwise::$implementations['rightShift'] = Core::curry(Bitwise::$implementations['rightShift']);
