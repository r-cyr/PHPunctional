<?php

namespace P;

use P\Maybe\Maybe;
use P\Maybe\Nothing;

class Collection {
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
 * Returns a new array with $f applied to every element
 *
 * @param callable $f
 * @param array $xs
 * @return array
 */
Collection::$implementations['map'] = function(callable $f, array $xs) {
  return \array_map($f, $xs);
};
Collection::$implementations['map'] = Core::curry(Collection::$implementations['map']);

/**
 * Returns a new array with the elements where the $f predicate returns true
 *
 * @param callable $f
 * @param array $xs
 * @return array
 */
Collection::$implementations['filter'] = function(callable $f, array $xs) {
  return \array_values(\array_filter($xs, $f));
};
Collection::$implementations['filter'] = Core::curry(Collection::$implementations['filter']);

/**
 * Reduce an array to a single value, beginning from the left
 *
 * @param callable $f
 * @param mixed $init
 * @param array $xs
 * @return mixed
 */
Collection::$implementations['foldl'] = function(callable $f, $init, array $xs) {
  return \array_reduce($xs, $f, $init);
};
Collection::$implementations['foldl'] = Core::curry(Collection::$implementations['foldl']);

/**
 * Reduce an array to a single value, beginning from the right
 *
 * @param callable $f
 * @param mixed $init
 * @param array $xs
 * @return mixed
 */
Collection::$implementations['foldr'] = function(callable $f, $init, array $xs) {
  $foldr = Collection::$implementations['foldr'];

  if (\count($xs) === 0) {
    return $init;
  }

  return \call_user_func($f, $xs[0], $foldr($f, $init, \array_slice($xs, 1)));
};
Collection::$implementations['foldr'] = Core::curry(Collection::$implementations['foldr']);

/**
 * Reduce an array, accumulating the intermediate results from the left
 *
 * @param callable $f
 * @param mixed $init
 * @param array $xs
 * @return mixed
 */
Collection::$implementations['scanl'] = function(callable $f, $init, array $xs) {
  $accumulator = [$init];
  $result = $init;

  foreach($xs as $x) {
    $result = \call_user_func($f, $result, $x);
    $accumulator[] = $result;
  }

  return $accumulator;
};
Collection::$implementations['scanl'] = Core::curry(Collection::$implementations['scanl']);

/**
 * Reduce an array, accumulating the intermediate results from the right
 *
 * @param callable $f
 * @param mixed $init
 * @param array $xs
 * @return mixed
 */
Collection::$implementations['scanr'] = function(callable $f, $init, array $xs) {
  $accumulator = [0];
  $result = $init;
  $length = \count($xs);

  for ($i = $length - 1; $i >= 0; $i--) {
    $result = \call_user_func($f, $xs[$i], $result);
    \array_unshift($accumulator, $result);
  }

  return $accumulator;
};
Collection::$implementations['scanr'] = Core::curry(Collection::$implementations['scanr']);

/**
 * Returns a new array with the element in reverse order
 *
 * @param array $xs
 * @return array
 */
Collection::$implementations['reverse'] = function(array $xs) {
  return \array_reverse($xs);
};

/**
 * Separates an array into two arrays, one with the elements where the predicate $f returns true,
 * the other with the elements where the predicate $f returns false
 *
 * @param callable $f
 * @param array $xs
 * @return array
 */
Collection::$implementations['partition'] = function(callable $f, array $xs) {
  $trues = [];
  $falses = [];

  foreach($xs as $x) {
    if (\call_user_func($f, $x)) {
      $trues[] = $x;
    } else {
      $falses[] = $x;
    }
  }

  return [$trues, $falses];
};
Collection::$implementations['partition'] = Core::curry(Collection::$implementations['partition']);

/**
 * Returns an array with $n times $a in it
 *
 * @param int $n
 * @param mixed $a
 * @return array
 */
Collection::$implementations['replicate'] = function($n, $a) {
  $result = [];

  for ($i = 0; $i < $n; $i++) {
    $result[] = $a;
  }

  return $result;
};
Collection::$implementations['replicate'] = Core::curry(Collection::$implementations['replicate']);

/**
 * Returns a new array where all the elements are separated by a $a element
 *
 * @param mixed $a
 * @param array $xs
 * @return array
 */
Collection::$implementations['intersperse'] = function($a, array $xs) {
  $tail = Collection::$implementations['tail'];

  if (count($xs) < 2) {
    return $xs;
  }

  $result = [$xs[0]];
  foreach($tail($xs) as $x) {
    $result[] = $a;
    $result[] = $x;
  }

  return $result;
};
Collection::$implementations['intersperse'] = Core::curry(Collection::$implementations['intersperse']);

/**
 * Concatenates two arrays
 *
 * @param array $xs
 * @param array $ys
 * @return array
 */
Collection::$implementations['concat'] = function(array $xs, array $ys) {
  return \array_merge($xs, $ys);
};
Collection::$implementations['concat'] = Core::curry(Collection::$implementations['concat']);

/**
 * Return the first element of an array wrapped in a Maybe
 *
 * @param array $xs
 * @return Maybe\Just|Nothing
 */
Collection::$implementations['head'] = function(array $xs) {
  return \count($xs) > 0 ? Maybe::of($xs[0])
                         : Nothing::instance();
};

/**
 * Returns a new array with all elements from $xs but the first
 *
 * @param array $xs
 * @return array
 */
Collection::$implementations['tail'] = function(array $xs) {
  return \count($xs) > 0 ? \array_slice($xs, 1)
                         : [];
};

/**
 * Returns the last element of $xs wrapped in a Maybe
 *
 * @param array $xs
 * @return Maybe\Just|Nothing
 */
Collection::$implementations['last'] = function(array $xs) {
  $length = \count($xs);
  return $length > 0 ? Maybe::of($xs[$length - 1])
                     : Nothing::instance();
};

/**
 * Returns all the elements of $xs but the last one
 *
 * @param array $xs
 * @return array
 */
Collection::$implementations['init'] = function(array $xs) {
  $length = \count($xs);

  return $length > 0 ? \array_slice($xs, 0, $length - 1)
                     : [];
};

/**
 * Returns the array $xs sorted using the comparator $f
 *
 * @param callable $f
 * @param array $xs
 * @return array
 */
Collection::$implementations['sortBy'] = function(callable $f, array $xs) {
  \usort($xs, $f);
  return $xs;
};
Collection::$implementations['sortBy'] = Core::curry(Collection::$implementations['sortBy']);

/**
 * Returns true if all the elements in $xs return true when passed to the predicate $f
 *
 * @param callable $f
 * @param array $xs
 * @return bool
 */
Collection::$implementations['all'] = function(callable $f, array $xs) {
  foreach($xs as $x) {
    if (!\call_user_func($f, $x)) {
      return false;
    }
  }

  return true;
};
Collection::$implementations['all'] = Core::curry(Collection::$implementations['all']);

/**
 * Returns true if any elements in $xs returns true when passed to the predicate $f
 *
 * @param callable $f
 * @param array $xs
 * @return bool
 */
Collection::$implementations['any'] = function(callable $f, array $xs) {
  foreach($xs as $x) {
    if (\call_user_func($f, $x)) {
      return true;
    }
  }

  return false;
};
Collection::$implementations['any'] = Core::curry(Collection::$implementations['any']);

/**
 * Returns the first element from $xs that returns true when passed to the predicate $f,
 * wrapped in a Maybe
 *
 * @param callable $f
 * @param array $xs
 * @return Maybe\Just|Nothing
 */
Collection::$implementations['find'] = function(callable $f, array $xs) {
  foreach($xs as $x) {
    if (call_user_func($f, $x)) {
      return Maybe::of($x);
    }
  }

  return Nothing::instance();
};
Collection::$implementations['find'] = Core::curry(Collection::$implementations['find']);

/**
 * Returns the index of the first element from $xs that returns true when passed to the predicate $f,
 * wrapped in a Maybe
 *
 * @param callable $f
 * @param array $xs
 * @return Maybe\Just|Nothing
 */
Collection::$implementations['findIndex'] = function(callable $f, array $xs) {
  foreach($xs as $index => $x) {
    if (\call_user_func($f, $x)) {
      return Maybe::of($index);
    }
  }

  return Nothing::instance();
};
Collection::$implementations['findIndex'] = Core::curry(Collection::$implementations['findIndex']);

/**
 * Returns the first $n elements of $xs
 *
 * @param int $n
 * @param array $xs
 * @return array
 */
Collection::$implementations['take'] = function($n, array $xs) {
  return \array_slice($xs, 0, $n);
};
Collection::$implementations['take'] = Core::curry(Collection::$implementations['take']);

/**
 * Returns the longest prefix of $xs that satisfies the predicate $f
 *
 * @param callable $f
 * @param array $xs
 * @return array
 */
Collection::$implementations['takeWhile'] = function(callable $f, array $xs) {
  $keep = true;
  $result = [];

  foreach($xs as $x) {
    if ($keep && !\call_user_func($f, $x)) {
      $keep = false;
    }
    if ($keep) {
      $result[] = $x;
    }
  }

  return $result;
};
Collection::$implementations['takeWhile'] = Core::curry(Collection::$implementations['takeWhile']);

/**
 * Returns a new array with the first $n elements removed
 *
 * @param int $n
 * @param array $xs
 * @return array
 */
Collection::$implementations['drop'] = function ($n, array $xs) {
  return \array_slice($xs, $n);
};
Collection::$implementations['drop'] = Core::curry(Collection::$implementations['drop']);


/**
 * Returns a new array where the first X elements satisfying the predicate $f are removed
 *
 * @param callable $f
 * @param array $xs
 * @return array
 */
Collection::$implementations['dropWhile'] = function (callable $f, array $xs) {
  $keep = false;
  $result = [];

  foreach($xs as $x) {
    if (!$keep && !\call_user_func($f, $x)) {
      $keep = true;
    }
    if ($keep) {
      $result[] = $x;
    }
  }

  return $result;
};
Collection::$implementations['dropWhile'] = Core::curry(Collection::$implementations['dropWhile']);

/**
 * Returns a new array of the result of $f applied to every element of both $xs and $ys
 *
 * @param callable $f
 * @param array $xs
 * @param array $ys
 * @return array
 */
Collection::$implementations['zipWith'] = function (callable $f, array $xs, array $ys) {
  return \array_map($f, $xs, $ys);
};
Collection::$implementations['zipWith'] = Core::curry(Collection::$implementations['zipWith']);
