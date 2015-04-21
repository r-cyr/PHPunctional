<?php
namespace P\Either;

use P\Applicative;

class Left extends Either
{
  private function __construct($a)
  {
    $this->a = $a;
  }

  /**
   * Returns true if current instance of Either is Left
   *
   * @return bool
   */
  public function isLeft()
  {
    return true;
  }

  /**
   * Returns true if current instance of Either is Right
   *
   * @return bool
   */
  public function isRight()
  {
    return false;
  }

  /**
   * Returns true on equality
   *
   * @param Either $either
   * @return bool
   */
  public function equals(Either $either)
  {
    return $either->isLeft() && $this->a === $either->a;
  }

  /**
   * Standard Functor map callable
   *
   * @param callable $f
   * @return Functor
   */
  public function map(callable $f)
  {
    return self::of($this->a);
  }

  /**
   * Sequential application
   *
   * @param Applicative $a
   * @return Applicative
   */
  public function ap(Applicative $a) {
    return $this;
  }

  /**
   * Sequentially compose two actions, passing any value produced by the first as an argument to the second.
   *
   * @param callable $f
   * @return Monad
   */
  public function bind(callable $f)
  {
    return self::of($this->a);
  }

  public function getOrElse(callable $f)
  {
    return \call_user_func($f, $this->a);
  }

  public static function of($a)
  {
    return new Left($a);
  }
}
