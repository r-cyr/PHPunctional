<?php

use P\Arithmetic;
use P\Core;
use P\Maybe\Maybe;
use P\Maybe\Nothing;

class MaybeTest extends PHPUnit_Framework_TestCase {

  public function test_functor_laws_identity()
  {
    $left = Maybe::of(42)->map('P\Core::identity');
    $right = Maybe::of(42);

    $this->assertTrue($left->equals($right));
  }

  public function test_functor_laws_composition()
  {
    $left = Maybe::of(40)->map(Core::compose('P\Arithmetic::succ', 'P\Arithmetic::succ'));
    $right = Maybe::of(40)->map('P\Arithmetic::succ')
                          ->map('P\Arithmetic::succ');

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_laws_identity()
  {
    $left = Maybe::of('P\Core::identity')->ap(Maybe::of(42));
    $right = Maybe::of(42);

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_laws_composition()
  {
    $left = Maybe::of('P\Core::compose')->ap(Maybe::of(Arithmetic::add(2)))
                                        ->ap(Maybe::of(Arithmetic::multiply(2)))
                                        ->ap(Maybe::of(20));
    $right = Maybe::of(Arithmetic::add(2))->ap(Maybe::of(Arithmetic::multiply(2))->ap(Maybe::of(20)));

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_laws_homomorphism()
  {
    $left = Maybe::of(Arithmetic::multiply(2))->ap(Maybe::of(21));
    $right = Maybe::of(Arithmetic::multiply(2, 21));

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_laws_interchange()
  {
    $left = Maybe::of(Arithmetic::multiply(2))->ap(Maybe::of(21));
    $right = Maybe::of(Core::apply(Core::_, 21))->ap(Maybe::of(Arithmetic::multiply(2)));

    $this->assertTrue($left->equals($right));
  }

  public function test_Monad_laws_left_identity()
  {
    $f = Core::compose('P\Maybe\Maybe::of', 'P\Core::identity');
    $left = Maybe::of(42)->bind($f);
    $right = $f(42);

    $this->assertTrue($left->equals($right));
  }

  public function test_Monad_laws_right_identity()
  {
    $left = Maybe::of(42)->bind('P\Maybe\Maybe::of');
    $right = Maybe::of(42);

    $this->assertTrue($left->equals($right));
  }

  public function test_Monad_laws_associativity()
  {
    $f = Core::compose('P\Maybe\Maybe::of', 'P\Core::identity');
    $g = Core::compose('P\Maybe\Maybe::of', 'P\Arithmetic::succ');
    $left = Maybe::of(41)->bind($f)
                         ->bind($g);
    $right = Maybe::of(41)->bind(function($x) use ($f, $g) { return $f($x)->bind($g); });

    $this->assertTrue($left->equals($right));
  }

  public function test_Functor_map_nothing()
  {
    $left = Nothing::instance()->map('P\Core::identity');
    $right = Nothing::instance();
    $this->assertTrue($left->equals($right));
  }

  public function test_Functor_map_just()
  {
    $left = Maybe::of(42)->map('P\Core::identity');
    $right = Maybe::of(42);
    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_ap_nothing()
  {
    $left = Maybe::of('P\Core::identity')->ap(Nothing::instance());
    $right = Nothing::instance();

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_ap_just()
  {
    $left = Maybe::of('P\Core::identity')->ap(Maybe::of(42));
    $right = Maybe::of(42);

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_ap_nothing_callable()
  {
    $left = Nothing::instance()->ap(Maybe::of(42));
    $right = Nothing::instance();

    $this->assertTrue($left->equals($right));
  }

  public function test_Monad_bind_nothing()
  {
    $left = Nothing::instance()->bind(Core::compose('P\Maybe\Maybe::of', 'P\Core::identity'));
    $right = Nothing::instance();

    $this->assertTrue($left->equals($right));
  }

  public function test_Monad_bind_just()
  {
    $left = Maybe::of(42)->bind(Core::compose('P\Maybe\Maybe::of', 'P\Core::identity'));
    $right = Maybe::of(42);

    $this->assertTrue($left->equals($right));
  }

}