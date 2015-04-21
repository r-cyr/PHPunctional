<?php
namespace P;

abstract class Monad extends Applicative
{
  /**
   * Sequentially compose two actions, passing any value produced by the first as an argument to the second.
   *
   * @param callable $f
   * @return Monad
   */
  public abstract function bind(callable $f);
}
