<?php
namespace P\Either;

use P\Monad;

abstract class Either extends Monad
{
  protected $a;

  public abstract function isLeft();

  public abstract function isRight();

  public abstract function equals(Either $either);

  public abstract function getOrElse(callable $f);

  public static function of($a)
  {
    return Right::of($a);
  }

  public static function fromNullable($a)
  {
    return $a === null ? Left::of($a)
                       : Right::of($a);
  }
}