<?php
namespace P\Validation;

use P\Applicative;

class Failure extends Validation
{
  private function __construct($a)
  {
    $this->a = $a;
  }

  /**
   * Returns true if current instance of Validation is Success
   *
   * @return bool
   */
  public function isSuccess()
  {
    return false;
  }

  /**
   * Returns true if current instance of Validation is Failure
   *
   * @return bool
   */
  public function isFailure()
  {
    return true;
  }

  /**
   * Returns true on equality
   *
   * @param Validation $validation
   * @return bool
   */
  public function equals(Validation $validation)
  {
    return $validation->isFailure() && $this->a === $validation->a;
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
    return $a->isFailure() ? self::of(array_merge($this->a, $a->a))
                           : self::of($this->a);
  }

  public function getOrElse(callable $f)
  {
    return \call_user_func($f, $this->a);
  }

  public static function of($a)
  {
    return new Failure($a);
  }
}
