<?php
namespace P\Either;

use P\Applicative;

class Right extends Either
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
    return false;
  }

  /**
   * Returns true if current instance of Either is Right
   *
   * @return bool
   */
  public function isRight()
  {
    return true;
  }

  /**
   * Returns true on equality
   *
   * @param Either $either
   * @return bool
   */
  public function equals(Either $either)
  {
    return $either->isRight() && $this->a === $either->a;
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

  public function getOrElse(callable $f)
  {
    return $this->a;
  }

  public static function of($a)
  {
    return new Right($a);
  }
}