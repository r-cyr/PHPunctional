<?php
namespace P;

abstract class Applicative extends Functor {
  /**
   * Sequential application
   *
   * @param Applicative $a
   * @return Applicative
   */
  public abstract function ap(Applicative $a);

  /**
   * Sequence actions, discarding the value of the first argument.
   *
   * @param Applicative $a
   * @return Applicative
   */
  public function seqr(Applicative $a)
  {
    return Core::liftA2(Core::constant('P\Core::identity'), $this, $a);
  }

  /**
   * Sequence actions, discarding the value of the second argument.
   *
   * @param Applicative $a
   * @return Applicative
   */
  public function seql(Applicative $a)
  {
    return Core::liftA2('P\Core::constant', $this, $a);
  }
}
