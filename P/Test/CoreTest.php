<?php

namespace P\Test;

use P\Arithmetic;
use P\Core;
use P\Maybe\Maybe;
use P\Maybe\Nothing;
use P\Relational;
use P\Test\Fixture\Foo;
use PHPUnit_Framework_TestCase;

function addition($x, $y) {
  return $x + $y;
}

function testFunction() {
  return 42;
}

class CoreTest extends PHPUnit_Framework_TestCase {

  public function test_curry_should_accept_strings_as_callable()
  {
    $curriedFunction = Core::curry('P\Test\testFunction');

    $this->assertThat($curriedFunction(), $this->equalTo(42));
  }

  public function test_curry_should_accept_static_method_strings_as_callable()
  {
    $curriedStaticMethod = Core::curry('P\Test\Fixture\Foo::staticMethod');

    $this->assertThat($curriedStaticMethod(), $this->equalTo(42));
  }

  public function test_curry_should_accept_static_method_arrays_methods_as_callable()
  {
    $curriedStaticMethod = Core::curry(['P\Test\Fixture\Foo', 'staticMethod']);

    $this->assertThat($curriedStaticMethod(), $this->equalTo(42));
  }

  public function test_curry_should_accept_instance_method_arrays_methods_as_callable()
  {
    $instance = new Foo();
    $curriedInstanceMethod = Core::curry([$instance, 'method']);

    $this->assertThat($curriedInstanceMethod(), $this->equalTo(42));
  }

  public function test_curry_should_accept_closures_as_callable()
  {
    $curriedClosure = Core::curry(function($x, $y) { return $x + $y; }, [2]);

    $this->assertThat($curriedClosure(40), $this->equalTo(42));
  }

  public function test_curry_should_accept_initial_arguments()
  {
    $curriedFunction = Core::curry('P\Test\addition', [2]);

    $this->assertThat($curriedFunction(40), $this->equalTo(42));
  }

  public function test_curry_should_accept_all_arguments()
  {
    $curriedFunction = Core::curry('P\Test\addition', [2, 40]);

    $this->assertThat($curriedFunction(), $this->equalTo(42));
  }

  public function test_curry_should_accept_placeholders()
  {
    $add2 = Arithmetic::add(Core::_, 2);
    $twoPlaceholderAdd = Arithmetic::add(Core::_, Core::_);

    $this->assertThat($add2(40), $this->equalTo(42));
    $this->assertThat($twoPlaceholderAdd(40, 2), $this->equalTo(42));
  }

  public function test_curry_should_force_a_callable_to_a_specific_arity()
  {
    $curriedFunction = Core::curry("max", [10, 42], 3);

    $this->assertThat($curriedFunction(12), $this->equalTo(42));
  }

  public function test_compose_should_compose_two_functions_together()
  {
    $composedFunction = Core::compose(
      function($x) { return $x + 2; },
      function($x) { return $x * 2; }
    );

    $this->assertThat($composedFunction(10), $this->equalTo(22));
  }

  public function test_flip_should_reverse_the_arguments_of_a_binary_callable()
  {
    $function = Core::curry(function($x, $y) { return $x / $y; });
    $flippedFunction = Core::flip($function);

    $this->assertThat($flippedFunction(2, 10), $this->equalTo(5));
  }

  public function test_identity_should_return_the_argument_passed()
  {
    $this->assertThat(Core::identity(42), $this->equalTo(42));
  }

  public function test_constant_should_return_the_first_argument_passed()
  {
    $this->assertThat(Core::constant(42, 10), $this->equalTo(42));
  }

  public function test_liftA_should_apply_a_unary_callable_to_an_applicative()
  {
    $applicative = Maybe::of(84);
    $callable = function($n) { return $n / 2; };

    $this->assertTrue(Core::liftA($callable, $applicative)->equals(Maybe::of(42)));
  }

  public function test_liftA2_should_apply_a_binary_callable_to_two_applicatives()
  {
    $applicative1 = Maybe::of(20);
    $applicative2 = Maybe::of(22);
    $callable = Core::curry(function($x, $y) { return $x + $y; });

    $this->assertTrue(Core::liftA2($callable, $applicative1, $applicative2)->equals(Maybe::of(42)));
  }

  public function test_liftA3_should_apply_a_ternary_callable_to_three_applicatives()
  {
    $applicative1 = Maybe::of(20);
    $applicative2 = Maybe::of(10);
    $applicative3 = Maybe::of(12);
    $callable = Core::curry(function($x, $y, $z) { return $x + $y + $z; });

    $this->assertTrue(Core::liftA3($callable, $applicative1, $applicative2, $applicative3)->equals(Maybe::of(42)));
  }

  public function test_map_should_apply_a_callable_to_a_functor()
  {
    $applicative = Maybe::of(21);

    $this->assertTrue(Core::map(function($x) { return $x * 2; }, $applicative)->equals(Maybe::of(42)));
  }

  public function test_ap_should_apply_a_lifted_callable_to_an_applicative()
  {
    $liftedCallable = Maybe::of(function($x) { return $x * 2; });
    $applicative = Maybe::of(21);

    $this->assertTrue(Core::ap($liftedCallable, $applicative)->equals(Maybe::of(42)));
  }

  public function test_sequence_should_return_a_monad_containing_the_results()
  {
    $allSuccess = Core::sequence('P\Maybe\Maybe', [
      Maybe::of(1), Maybe::of(1), Maybe::of(1), Maybe::of(1)
    ]);

    $withFailure = Core::sequence('P\Maybe\Maybe', [
      Maybe::of(1), Maybe::of(1), Nothing::instance(), Maybe::of(1)
    ]);

    $this->assertTrue($allSuccess->equals(Maybe::of([1, 1, 1, 1])));
    $this->assertTrue($withFailure->equals(Nothing::instance()));
  }

  public function test_mapM_should_return_a_monad_containing_the_results_with_a_callable_applied()
  {
    $testCallable = function($x) { return $x === 1 ? Maybe::of($x + 4) : Nothing::instance(); };

    $allSuccess = Core::mapM('P\Maybe\Maybe', $testCallable, [
      1, 1, 1, 1
    ]);

    $withFailure = Core::mapM('P\Maybe\Maybe', $testCallable, [
      1, 1, 2, 1
    ]);

    $this->assertTrue($allSuccess->equals(Maybe::of([5, 5, 5, 5])));
    $this->assertTrue($withFailure->equals(Nothing::instance()));
  }

  public function test_filterM_should_return_a_monad_containing_the_filtered_results_of_the_applied_callable()
  {
    $testCallable = function($x) { return is_bool($x) ? Maybe::of($x) : Nothing::instance(); };

    $allSuccess = Core::filterM('P\Maybe\Maybe', $testCallable, [
      true, true, false, false
    ]);

    $withFailure = Core::filterM('P\Maybe\Maybe', $testCallable, [
      true, true, "OMG", false
    ]);

    $this->assertTrue($allSuccess->equals(Maybe::of([true, true])));
    $this->assertTrue($withFailure->equals(Nothing::instance()));
  }

  public function test_composeM_should_do_the_left_to_right_kleisli_composition_of_monads()
  {
    $aToMb = function($x) { return Maybe::of($x * 2); };
    $bToMc = function($x) { return Maybe::of($x + 2); };
    $aToMc = Core::composeM($aToMb, $bToMc);

    $this->assertTrue(Maybe::of(20)->bind($aToMc)->equals(Maybe::of(42)));
  }

  public function test_join_should_remove_a_level_of_monadic_structure()
  {
    $twoLevelsMonad = Maybe::of(Maybe::of(42));

    $this->assertTrue(Core::join($twoLevelsMonad)->equals(Maybe::of(42)));
  }

  public function test_construct_should_create_an_object_using_new()
  {
    $pointConstructor = Core::construct('P\Test\Fixture\Point');
    $point = $pointConstructor(10, 20);

    $this->assertThat($point, $this->isInstanceOf('P\Test\Fixture\Point'));
    $this->assertThat($point->x(), $this->equalTo(10));
    $this->assertThat($point->y(), $this->equalTo(20));
  }

  public function test_fix_should_return_the_fixedpoint_of_a_callable()
  {
    $factorial = Core::fix(function($partial) {
      return function($n) use ($partial) {
        return $n === 0 ? 1 : $n  * $partial($n - 1);
      };
    });

    $fibonacci = Core::fix(function($partial) {
      return function($n) use ($partial) {
        return $n <= 1 ? $n : $partial($n - 1) + $partial($n - 2);
      };
    });

    $this->assertThat($factorial(5), $this->equalTo(120));
    $this->assertThat($fibonacci(10), $this->equalTo(55));
  }

  public function test_conditions_should_return_the_result_of_the_callable_associated_with_the_first_predicate_that_returns_true()
  {
    $myCondition = Core::conditions([
      [Relational::equal(10),  'P\Core::identity'],
      [Relational::equal(20),  Core::constant(42)],
      [Core::constant(true),   Core::constant(100)]
    ]);

    $this->assertThat($myCondition(10), $this->equalTo(10));
    $this->assertThat($myCondition(20), $this->equalTo(42));
    $this->assertThat($myCondition(42), $this->equalTo(100));
  }

  public function test_property_should_return_the_property_of_an_array_or_object_wrapped_in_a_maybe()
  {
    $testArray = ['foo' => 42];
    $testObject = new \stdClass();
    $testObject->foo = 42;

    $nestedTest = new \stdClass();
    $nestedTest->foo = ['bar' => 42];

    $fooProperty = Core::property('foo');
    $barProperty = Core::property('bar');

    $nestedProperty = Core::composeM($fooProperty, $barProperty);

    $this->assertTrue($fooProperty($testArray)->equals(Maybe::of(42)));
    $this->assertTrue($barProperty($testArray)->equals(Nothing::instance()));
    $this->assertTrue($fooProperty($testObject)->equals(Maybe::of(42)));
    $this->assertTrue($barProperty($testObject)->equals(Nothing::instance()));
    $this->assertTrue($nestedProperty($nestedTest)->equals(Maybe::of(42)));
  }

  public function test_apply_should_apply_a_callable_to_an_argument() {
    $this->assertThat(Core::apply(Arithmetic::multiply(2), 21), $this->equalTo(42));
  }

}
