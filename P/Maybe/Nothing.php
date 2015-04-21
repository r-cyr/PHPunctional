<?php
namespace P\Maybe;

use P\Applicative;

class Nothing extends Maybe
{
  private static $instance;

  private function __construct()
  {
  }

  /**
   * Returns true if current instance of Maybe is Just
   *
   * @return bool
   */
  public function isJust()
  {
    return false;
  }

  /**
   * Returns true if current instance of Maybe is Nothing
   *
   * @return bool
   */
  public function isNothing()
  {
    return true;
  }

  /**
   * Returns true on equality
   *
   * @param Maybe $maybe
   * @return mixed
   */
  public function equals(Maybe $maybe)
  {
    return $maybe->isNothing();
  }

  /**
   * Standard Functor map callable
   *
   * @param callable $f
   * @return Functor
   */
  public function map(callable $f)
  {
    return self::$instance;
  }

  /**
   * Sequential application
   *
   * @param Applicative $a
   * @return Applicative
   */
  public function ap(Applicative $a) {
    return self::$instance;
  }

  /**
   * Sequentially compose two actions, passing any value produced by the first as an argument to the second.
   *
   * @param callable $f
   * @return Monad
   */
  public function bind(callable $f)
  {
    return self::$instance;
  }

  public function getOrDefault($default = null)
  {
    return $default;
  }

  public static function instance() {
    if (!isset(self::$instance)) {
      self::$instance = new Nothing();
    }

    return self::$instance;
  }
}
