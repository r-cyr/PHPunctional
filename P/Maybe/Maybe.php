<?php
namespace P\Maybe;

use P\Monad;

abstract class Maybe extends Monad
{
  protected $a;

  public abstract function isJust();

  public abstract function isNothing();

  public abstract function equals(Maybe $maybe);

  public abstract function getOrDefault($default = null);

  public static function of($a)
  {
    return Just::of($a);
  }

  public static function fromNullable($a)
  {
    return $a === null ? Nothing::instance()
                       : Just::of($a);
  }
}