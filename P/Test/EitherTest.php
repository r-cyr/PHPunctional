<?php

use P\Core;
use P\Either\Either;
use P\Arithmetic;
use P\Either\Left;

class EitherTest extends PHPUnit_Framework_TestCase {

  public function test_functor_laws_identity()
  {
    $left = Either::of(42)->map('P\Core::identity');
    $right = Either::of(42);

    $this->assertTrue($left->equals($right));
  }

  public function test_functor_laws_composition()
  {
    $left = Either::of(40)->map(Core::compose('P\Arithmetic::succ', 'P\Arithmetic::succ'));
    $right = Either::of(40)->map('P\Arithmetic::succ')
                           ->map('P\Arithmetic::succ');

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_laws_identity()
  {
    $left = Either::of('P\Core::identity')->ap(Either::of(42));
    $right = Either::of(42);

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_laws_composition()
  {
    $left = Either::of('P\Core::compose')->ap(Either::of(Arithmetic::add(2)))
                                         ->ap(Either::of(Arithmetic::multiply(2)))
                                         ->ap(Either::of(20));
    $right = Either::of(Arithmetic::add(2))->ap(Either::of(Arithmetic::multiply(2))->ap(Either::of(20)));

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_laws_homomorphism()
  {
    $left = Either::of(Arithmetic::multiply(2))->ap(Either::of(21));
    $right = Either::of(Arithmetic::multiply(2, 21));

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_laws_interchange()
  {
    $left = Either::of(Arithmetic::multiply(2))->ap(Either::of(21));
    $right = Either::of(Core::apply(Core::_, 21))->ap(Either::of(Arithmetic::multiply(2)));

    $this->assertTrue($left->equals($right));
  }

  public function test_Monad_laws_left_identity()
  {
    $f = Core::compose('P\Either\Either::of', 'P\Core::identity');
    $left = Either::of(42)->bind($f);
    $right = $f(42);

    $this->assertTrue($left->equals($right));
  }

  public function test_Monad_laws_right_identity()
  {
    $left = Either::of(42)->bind('P\Either\Either::of');
    $right = Either::of(42);

    $this->assertTrue($left->equals($right));
  }

  public function test_Monad_laws_associativity()
  {
    $f = Core::compose('P\Either\Either::of', 'P\Core::identity');
    $g = Core::compose('P\Either\Either::of', 'P\Arithmetic::succ');
    $left = Either::of(41)->bind($f)
                          ->bind($g);
    $right = Either::of(41)->bind(function($x) use ($f, $g) { return $f($x)->bind($g); });

    $this->assertTrue($left->equals($right));
  }

  public function test_Functor_map_left()
  {
    $left = Left::of("Failure")->map('P\Core::identity');
    $right = Left::of("Failure");
    $this->assertTrue($left->equals($right));
  }

  public function test_Functor_map_right()
  {
    $left = Either::of(42)->map('P\Core::identity');
    $right = Either::of(42);
    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_ap_left()
  {
    $left = Either::of('P\Core::identity')->ap(Left::of("Failure"));
    $right = Left::of("Failure");

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_ap_right()
  {
    $left = Either::of('P\Core::identity')->ap(Either::of(42));
    $right = Either::of(42);

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_ap_left_callable()
  {
    $left = Left::of("Failure")->ap(Either::of(42));
    $right = Left::of("Failure");

    $this->assertTrue($left->equals($right));
  }

  public function test_Monad_bind_left()
  {
    $left = Left::of("Failure")->bind(Core::compose('P\Either\Either::of', 'P\Core::identity'));
    $right = Left::of("Failure");

    $this->assertTrue($left->equals($right));
  }

  public function test_Monad_bind_right()
  {
    $left = Either::of(42)->bind(Core::compose('P\Either\Either::of', 'P\Core::identity'));
    $right = Either::of(42);

    $this->assertTrue($left->equals($right));
  }

}