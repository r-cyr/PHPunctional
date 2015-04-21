<?php
namespace P;

use P\Maybe\Just;
use P\Maybe\Nothing;

class Core {
  const _ = "__CALLABLE_ARGUMENT_PLACEHOLDER__";

  public static $implementations = ['private' => []];

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
 * Converts an uncurried callable to a curried one
 *
 * @param callable $f                Callable to curry
 * @param mixed[]  $initialArguments Initial arguments
 * @param int      $forcedArity      Force a specific arity (Useful for variadic callables)
 * @return callable
 */
Core::$implementations['curry'] = function(callable $f, array $initialArguments = [], $forcedArity = null) {
  $findNumberOfParameters = Core::$implementations['private']['findNumberOfParameters'];
  $doCurry = Core::$implementations['private']['doCurry'];
  $arity = isset($forcedArity) ? $forcedArity
                               : $findNumberOfParameters($f);

  if ($arity === 0) {
    return function() use ($f) {
      return \call_user_func($f);
    };
  }

  return $doCurry($f, $initialArguments, $arity);
};

Core::$implementations['private']['doCurry'] = function(callable $f, array $arguments, $arity) {
  $doCurry = Core::$implementations['private']['doCurry'];

  return function() use ($doCurry, $f, $arguments, $arity) {
    $allArguments = \array_merge($arguments, \func_get_args());
    $numberOfAllArguments = \count($allArguments);
    $actualArguments = \array_filter($allArguments, function($argument) { return $argument !== Core::_; });
    $numberOfActualArguments = \count($actualArguments);

    if ($numberOfActualArguments > $arity) {
      throw \Exception("Too many arguments");
    }

    if ($numberOfActualArguments === $arity) {
      $hasPlaceholders = \count($allArguments) > $numberOfActualArguments;

      if ($hasPlaceholders) {
        $numberOfPlaceHolders = $numberOfAllArguments - $numberOfActualArguments;
        $argumentsToFill = \array_slice($actualArguments, -$numberOfPlaceHolders);

        $placeholderIndex = 0;
        $argumentIndex = 0;
        while ($placeholderIndex < $numberOfPlaceHolders) {
          if ($allArguments[$argumentIndex] == Core::_) {
            $allArguments[$argumentIndex] = $argumentsToFill[$placeholderIndex];
            $placeholderIndex++;
          }
          $argumentIndex++;
        }
        return \call_user_func_array($f, $allArguments);
      } else {
        return \call_user_func_array($f, $allArguments);
      }
    }

    return $doCurry($f, $allArguments, $arity);
  };
};

Core::$implementations['private']['findNumberOfParameters'] = function(callable $f) {
  if (\is_string($f) && \strpos($f, '::') !== false) {
    $f = \explode('::', $f);
  }

  if (\is_array($f)) {
    $reflectionMethod = new \ReflectionMethod($f[0], $f[1]);
    return $reflectionMethod->getNumberOfParameters();
  }

  $reflectionFunction = new \ReflectionFunction($f);
  return $reflectionFunction->getNumberOfParameters();
};

/**
 * Composes 2 callables together
 *
 * @param callable $f First callable to compose
 * @param callable $g Second callable to compose
 * @param mixed $a
 * @return callable
 */
Core::$implementations['compose'] = function (callable $f, callable $g, $a) {
  return \call_user_func($f, call_user_func($g, $a));
};
Core::$implementations['compose'] = \call_user_func(Core::$implementations['curry'], Core::$implementations['compose']);

/**
 * Flips the first two arguments of a callable
 *
 * @param callable $f Callable on which to flip the first two arguments
 * @param mixed $x
 * @param mixed $y
 * @return callable
 */
Core::$implementations['flip'] = function(callable $f, $x, $y) {
  return \call_user_func(\call_user_func($f, $y), $x);
};
Core::$implementations['flip'] = Core::curry(Core::$implementations['flip']);

/**
 * Returns the argument it is called with
 *
 * @param $a mixed Value to return
 * @return mixed
 */
Core::$implementations['identity'] = function($a) {
  return $a;
};

/**
 * Returns the first argument
 *
 * @param $a mixed First argument
 * @param $b mixed Second argument
 * @return mixed
 */
Core::$implementations['constant'] = function($a, $b) {
  return $a;
};
Core::$implementations['constant'] = Core::curry(Core::$implementations['constant']);

/**
 * Lifts a unary callable
 *
 * @param callable $f    Unary callable to lift
 * @param Applicative $a Applicative on which to apply the callable
 * @return Applicative
 */
Core::$implementations['liftA'] = function(callable $f, Applicative $a) {
  return $a->map($f);
};
Core::$implementations['liftA'] = Core::curry(Core::$implementations['liftA']);

/**
 * Lifts a binary callable
 *
 * @param callable $f     Binary callable to lift
 * @param Applicative $a1 First Applicative on which to apply the callable
 * @param Applicative $a2 Second Applicative on which to apply the callable
 * @return Applicative
 */
Core::$implementations['liftA2'] = function(callable $f, Applicative $a1, Applicative $a2) {
  return $a1->map($f)->ap($a2);
};
Core::$implementations['liftA2'] = Core::curry(Core::$implementations['liftA2']);

/**
 * Lifts a ternary callable
 *
 * @param callable $f     Ternary callable to lift
 * @param Applicative $a1 First Applicative on which to apply the callable
 * @param Applicative $a2 Second Applicative on which to apply the callable
 * @param Applicative $a3 Third Applicative on which to apply the callable
 * @return Applicative
 */
Core::$implementations['liftA3'] = function(callable $f, Applicative $a1, Applicative $a2, Applicative $a3) {
  return $a1->map($f)->ap($a2)->ap($a3);
};
Core::$implementations['liftA3'] = Core::curry(Core::$implementations['liftA3']);

/**
 * Applies a callable to a Functor
 *
 * @param callable $f Callable to apply to the Functor
 * @param Functor $a  Functor on which to apply the callable
 * @return Functor
 */
Core::$implementations['map'] = function(callable$f, $a) {
  if (\is_array($a)) {
    return Collection::map($f, $a);
  }
  
  return $a->map($f);
};
Core::$implementations['map'] = Core::curry(Core::$implementations['map']);

/**
 * Applies a lifted callable to an Applicative
 *
 * @param Applicative $f
 * @param Applicative $b
 * @return mixed
 */
Core::$implementations['ap'] = function(Applicative $f, Applicative $b) {
  return $f->ap($b);
};
Core::$implementations['ap'] = Core::curry(Core::$implementations['ap']);

/**
 * Evaluates each action in the sequence from left to right, and collects the results
 *
 * @param string $monadClass Qualified name of the monad
 * @param Monad[] $xs        Array of Monads to sequence
 * @return Monad
 */
Core::$implementations['sequence'] = function($monadClass, array $xs) {
  $initial = \call_user_func([$monadClass, 'of'], []);

  return \array_reduce($xs, function($acc, $m) use ($monadClass) {
    return $acc->bind(function($val1) use ($monadClass, $m) {
      return $m->bind(function($val2) use ($monadClass, $val1) {
        return \call_user_func([$monadClass, 'of'], \array_merge($val1, [$val2]));
      });
    });
  }, $initial);
};
Core::$implementations['sequence'] = Core::curry(Core::$implementations['sequence']);

/**
 * Equivalent to sequence + map
 *
 * @param string $monadClass Qualified name of the Monad
 * @param Monad[] $xs        Array of elements
 * @param callable $f        Callable that returns a lifted value
 * @return Monad
 */
Core::$implementations['mapM'] = function($monadClass, callable $f, array $xs) {
  $sequence = Core::$implementations['sequence'];
  return $sequence($monadClass, \array_map($f, $xs));
};
Core::$implementations['mapM'] = Core::curry(Core::$implementations['mapM']);

/**
 * Applies $f on the elements and filter them
 *
 * @param string $monadClass Qualified name of the Monad
 * @param callable $f        Callable that returns a lifted predicate
 * @param array $xs          Array of elements
 * @return Monad
 */
Core::$implementations['filterM'] = function($monadClass, callable $f, array $xs) {
  $filterM = Core::$implementations['filterM'];

  if (\count($xs) === 0) {
    return \call_user_func([$monadClass, 'of'], []);
  }

  $head = $xs[0];
  $tail = \array_slice($xs, 1);

  $m = \call_user_func($f, $head);

  return $m->bind(function($isTrue) use ($filterM, $monadClass, $f, $head, $tail) {
    $remaining = $filterM($monadClass, $f, $tail);

    return $remaining->bind(function($val) use ($monadClass, $head, $isTrue) {
      return \call_user_func([$monadClass, 'of'], $isTrue ? \array_merge([$head], $val) : $val);
    });
  });
};
Core::$implementations['filterM'] = Core::curry(Core::$implementations['filterM']);

/**
 * Left-to-right Kleisli composition of Monads.
 *
 * @param callable $f First callable to compose
 * @param callable $g Second callable to compose
 * @param Monad $a
 * @return callable
 */
Core::$implementations['composeM'] = function(callable $f, callable $g, $a) {
  $return  = \call_user_func($f, $a);

  return $return->bind($g);
};
Core::$implementations['composeM'] = Core::curry(Core::$implementations['composeM']);

/**
 * Removes one level of Monad
 *
 * @param Monad $m The Monad on which to remove a level (Unwraps)
 * @return Monad
 */
Core::$implementations['join'] = function(Monad $m) {
  return $m->bind('P\Core::identity');
};

/**
 * Returns a curried function that constructs an object using its constructor
 *
 * @param string $className
 * @return callable
 */
Core::$implementations['construct'] = function($className) {
  $reflectionClass = new \ReflectionClass($className);
  $contructor = $reflectionClass->getConstructor();
  $numberOfArguments = $contructor->getNumberOfParameters();

  return Core::curry(function() use ($reflectionClass) {
    return $reflectionClass->newInstanceargs(\func_get_args());
  }, [], $numberOfArguments);
};

/**
 * Return the fixed point of a callable (Y Combinator)
 *
 * @param callable $f
 * @return callable
 */
Core::$implementations['fix'] = function(callable $f) {
  $tmp = function($g) use ($f) { return function($n) use ($f, $g) { return \call_user_func(\call_user_func($f, \call_user_func($g, $g)), $n); }; };
  return $tmp(function($g) use ($f) { return function($n) use ($f, $g) { return \call_user_func(\call_user_func($f, \call_user_func($g, $g)), $n); }; });
};

/**
 * Returns the result of the callable associated with the first predicate that returns true
 *
 * @param array $xs
 * @return callable
 */
Core::$implementations['conditions'] = function(array $xs) {
  return function($a) use ($xs) {
    $numberOfConditions = \count($xs);

    for ($index = 0; $index < $numberOfConditions; $index++) {
      list($predicate, $condition) = $xs[$index];

      if (\call_user_func($predicate, $a)) {
        return \call_user_func($condition, $a);
      }
    }

    throw new \Exception("No condition was true");
  };
};

/**
 * Returns the $name property of an object or array
 *
 * @param string $name
 * @param array|object $a
 * @return callable
 */
Core::$implementations['property'] = function($name, $a) {
  if (\is_object($a)) {
    return \property_exists($a, $name) ? Just::of($a->{$name})
                                       : Nothing::instance();
  } else if (\is_array($a)) {
    return isset($a[$name]) ? Just::of($a[$name])
                            : Nothing::instance();
  }
};
Core::$implementations['property'] = Core::curry(Core::$implementations['property']);

/**
 * Return the callable $f applied to $x
 *
 * @param callable $f
 * @param mixed $x
 * @return mixed
 */
Core::$implementations['apply'] = function(callable $f, $x) {
  return \call_user_func($f, $x);
};
Core::$implementations['apply'] = Core::curry(Core::$implementations['apply']);
