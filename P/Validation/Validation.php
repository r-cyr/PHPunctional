<?php
namespace P\Validation;

use P\Applicative;

abstract class Validation extends Applicative
{
  protected $a;

  public abstract function isSuccess();

  public abstract function isFailure();

  public abstract function equals(Validation $validation);

  public abstract function getOrElse(callable $f);

  public static function of($a)
  {
    return Success::of($a);
  }

  public static function fromNullable($a)
  {
    return $a === null ? Failure::of($a)
                       : Success::of($a);
  }
}
