<?php
namespace P;

abstract class Functor
{
  /**
   * Standard Functor map callable
   *
   * @param callable $f
   * @return Functor|Applicative|Monad
   */
  public abstract function map(callable $f);
}
