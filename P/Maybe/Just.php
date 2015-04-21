<?php
namespace P\Maybe;

use P\Applicative;

class Just extends Maybe
{
  private function __construct($a)
  {
    $this->a = $a;
  }

  /**
   * Returns true if current instance of Maybe is Just
   *
   * @return bool
   */
  public function isJust()
  {
    return true;
  }

  /**
   * Returns true if current instance of Maybe is Nothing
   *
   * @return bool
   */
  public function isNothing()
  {
    return false;
  }

  /**
   * Returns true on equality
   *
   * @param Maybe $maybe
   * @return mixed
   */
  public function equals(Maybe $maybe)
  {
    return $maybe->isJust() && $this->a === $maybe->a;
  }

  /**
   * Standard Functor map callable
   *
   * @param callable $f
   * @return Functor
   */
  public function map(callable $f)
  {
    return self::of(\call_user_func($f, $this->a));
  }

  /**
   * Sequential application
   *
   * @param Applicative $a
   * @return Applicative
   */
  public function ap(Applicative $a) {
    return $a->map($this->a);
  }

  /**
   * Sequentially compose two actions, passing any value produced by the first as an argument to the second.
   *
   * @param callable $f
   * @return Monad
   */
  public function bind(callable $f)
  {
    return \call_user_func($f, $this->a);
  }

  public function getOrDefault($default = null)
  {
    return $this->a;
  }

  public static function of($a)
  {
    return new Just($a);
  }
}