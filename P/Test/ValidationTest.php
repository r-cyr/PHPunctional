<?php

use P\Core;
use P\Validation\Validation;
use P\Arithmetic;
use P\Validation\Failure;

class ValidationTest extends PHPUnit_Framework_TestCase {

  public function test_functor_laws_identity()
  {
    $left = Validation::of(42)->map('P\Core::identity');
    $right = Validation::of(42);

    $this->assertTrue($left->equals($right));
  }

  public function test_functor_laws_composition()
  {
    $left = Validation::of(40)->map(Core::compose('P\Arithmetic::succ', 'P\Arithmetic::succ'));
    $right = Validation::of(40)->map('P\Arithmetic::succ')
                               ->map('P\Arithmetic::succ');

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_laws_identity()
  {
    $left = Validation::of('P\Core::identity')->ap(Validation::of(42));
    $right = Validation::of(42);

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_laws_composition()
  {
    $left = Validation::of('P\Core::compose')->ap(Validation::of(Arithmetic::add(2)))
                                             ->ap(Validation::of(Arithmetic::multiply(2)))
                                             ->ap(Validation::of(20));
    $right = Validation::of(Arithmetic::add(2))->ap(Validation::of(Arithmetic::multiply(2))->ap(Validation::of(20)));

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_laws_homomorphism()
  {
    $left = Validation::of(Arithmetic::multiply(2))->ap(Validation::of(21));
    $right = Validation::of(Arithmetic::multiply(2, 21));

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_laws_interchange()
  {
    $left = Validation::of(Arithmetic::multiply(2))->ap(Validation::of(21));
    $right = Validation::of(Core::apply(Core::_, 21))->ap(Validation::of(Arithmetic::multiply(2)));

    $this->assertTrue($left->equals($right));
  }

  public function test_Functor_map_left()
  {
    $left = Failure::of(["Failure"])->map('P\Core::identity');
    $right = Failure::of(["Failure"]);
    $this->assertTrue($left->equals($right));
  }

  public function test_Functor_map_right()
  {
    $left = Validation::of(42)->map('P\Core::identity');
    $right = Validation::of(42);
    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_ap_left()
  {
    $left = Validation::of('P\Core::identity')->ap(Failure::of(["Failure"]));
    $right = Failure::of(["Failure"]);

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_ap_right()
  {
    $left = Validation::of('P\Core::identity')->ap(Validation::of(42));
    $right = Validation::of(42);

    $this->assertTrue($left->equals($right));
  }

  public function test_Applicative_ap_left_callable()
  {
    $left = Failure::of(["Failure"])->ap(Validation::of(42));
    $right = Failure::of(["Failure"]);

    $this->assertTrue($left->equals($right));
  }

  public function test_Validation_should_accumulate_failures()
  {
    $left = Validation::of("Hello")->seql(Failure::of(["Failure 1"]))
                                   ->seql(Validation::of("Success"))
                                   ->seql(Failure::of(["Failure 2"]));
    $right = Failure::of(["Failure 1", "Failure 2"]);

    $this->assertTrue($left->equals($right));
  }

}