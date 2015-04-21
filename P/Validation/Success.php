<?php
namespace P\Validation;

use P\Applicative;

class Success extends Validation
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
    return true;
  }

  /**
   * Returns true if current instance of Validation is Failure
   *
   * @return bool
   */
  public function isFailure()
  {
    return false;
  }

  /**
   * Returns true on equality
   *
   * @param Validation $validation
   * @return bool
   */
  public function equals(Validation $validation)
  {
    return $validation->isSuccess() && $this->a === $validation->a;
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
    return $this->isFailure() ? $a
                              : $a->map($this->a);
  }

  public function getOrElse(callable $f)
  {
    return $this->a;
  }

  public static function of($a)
  {
    return new Success($a);
  }
}
