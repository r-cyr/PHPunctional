<?php

namespace P\Test;

use P\Arithmetic;
use P\Collection;
use P\Core;
use P\Maybe\Maybe;
use P\Maybe\Nothing;
use P\Relational;
use PHPUnit_Framework_TestCase;

class CollectionTest extends PHPUnit_Framework_TestCase {

  public function test_map_should_return_a_new_array_with_a_callable_applied_to_every_element()
  {
    $this->assertThat(Collection::map(Arithmetic::multiply(2), [1, 2, 3, 4]), $this->equalTo([2, 4, 6, 8]));
  }

  public function test_filter_should_return_a_new_array_filtered_with_callable()
  {
    $this->assertThat(Collection::filter(Relational::greaterThan(Core::_, 2), [1, 2, 3, 4]), $this->equalTo([3, 4]));
  }

  public function test_foldl_should_reduce_an_array_to_a_single_element_beginning_from_the_left()
  {
    $this->assertThat(Collection::foldl('P\Arithmetic::add', 0, [1, 2, 3, 4]), $this->equalTo(10));
  }

  public function test_foldr_should_reduce_an_array_to_a_single_element_beginning_from_the_right()
  {
    $this->assertThat(Collection::foldr('P\Arithmetic::add', 0, [1, 2, 3, 4]), $this->equalTo(10));
  }

  public function test_scanl_should_reduce_an_array_accumulating_the_intermediate_results_from_the_left()
  {
    $this->assertThat(Collection::scanl('P\Arithmetic::add', 0, [1, 2, 3, 4]), $this->equalTo([0, 1, 3, 6, 10]));
  }

  public function test_scanr_should_reduce_an_array_accumulating_the_intermediate_results_from_the_right()
  {
    $this->assertThat(Collection::scanr('P\Arithmetic::add', 0, [1, 2, 3, 4]), $this->equalTo([10, 9, 7, 4, 0]));
  }

  public function test_reverse_should_return_an_array_with_its_elements_reversed()
  {
    $this->assertThat(Collection::reverse([1, 2, 3, 4]), $this->equalTo([4, 3, 2, 1]));
  }

  public function test_partition_should_return_an_array_with_the_elements_that_satisfies_the_callable_and_another_with_the_rest()
  {
    $this->assertThat(Collection::partition(Relational::greaterThan(Core::_, 2), [1, 2, 3, 4]), $this->equalTo([[3, 4], [1, 2]]));
  }

  public function test_replicate_should_return_an_array_of_N_times_some_value()
  {
    $this->assertThat(Collection::replicate(4, 1), $this->equalTo([1, 1, 1, 1]));
  }

  public function test_intersperse_should_return_an_array_where_the_elements_are_separated_by_some_value()
  {
    $this->assertThat(Collection::intersperse(1, [1, 2, 3, 4]), $this->equalTo([1, 1, 2, 1, 3, 1, 4]));
  }

  public function test_concat_should_concatenate_two_arrays()
  {
    $this->assertThat(Collection::concat([1, 2], [3, 4]), $this->equalTo([1, 2, 3, 4]));
  }

  public function test_head_should_return_the_first_element_of_an_array_wrapped_in_a_maybe()
  {
    $this->assertTrue(Collection::head([1, 2])->equals(Maybe::of(1)));
    $this->assertTrue(Collection::head([])->equals(Nothing::instance()));
  }

  public function test_tail_should_return_all_the_elements_of_an_array_except_the_first()
  {
    $this->assertThat(Collection::tail([1, 2, 3, 4]), $this->equalTo([2, 3, 4]));
  }

  public function test_last_should_return_the_last_element_of_an_array_wrapped_in_a_maybe()
  {
    $this->assertTrue(Collection::last([1, 2])->equals(Maybe::of(2)));
    $this->assertTrue(Collection::last([])->equals(Nothing::instance()));
  }

  public function test_init_should_return_all_the_elements_of_an_array_except_the_last()
  {
    $this->assertThat(Collection::init([1, 2, 3, 4]), $this->equalTo([1, 2, 3]));
  }

  public function test_sortBy_should_return_an_array_sorted_using_a_comparator_callable()
  {
    $comparator = 'P\Arithmetic::substract';
    $this->assertThat(Collection::sortBy($comparator, [4, 2, 5, 1, 3]), $this->equalTo([1, 2, 3, 4, 5]));
  }

  public function test_all_should_return_true_if_all_the_elements_satisfy_the_predicate()
  {
    $predicate = 'P\Core::identity';
    $this->assertThat(Collection::all($predicate, [true, true, true]), $this->equalTo(true));
    $this->assertThat(Collection::all($predicate, [true, false, true]), $this->equalTo(false));
  }

  public function test_any_should_return_true_if_any_of_the_elements_satisfies_the_predicate()
  {
    $predicate = 'P\Core::identity';
    $this->assertThat(Collection::any($predicate, [false, false, true]), $this->equalTo(true));
    $this->assertThat(Collection::any($predicate, [false, false, false]), $this->equalTo(false));
  }

  public function test_find_should_return_a_found_element_wrapped_in_a_maybe()
  {
    $this->assertTrue(Collection::find(Relational::equal(42), [23, 545, 42, 45])->equals(Maybe::of(42)));
    $this->assertTrue(Collection::find(Relational::equal(42), [23, 545, 45])->equals(Nothing::instance()));
  }

  public function test_findIndex_should_return_the_index_of_a_found_element_wrapped_in_a_maybe()
  {
    $this->assertTrue(Collection::findIndex(Relational::equal(42), [23, 545, 42, 45])->equals(Maybe::of(2)));
    $this->assertTrue(Collection::findIndex(Relational::equal(42), [23, 545, 45])->equals(Nothing::instance()));
  }

  public function test_take_should_return_the_first_X_elements_of_an_array()
  {
    $this->assertThat(Collection::take(2, [1, 2, 3, 4]), $this->equalTo([1, 2]));
  }

  public function test_the_longest_prefix_of_an_array_that_satisfies_a_predicate()
  {
    $this->assertThat(Collection::takeWhile(Relational::lessThan(Core::_, 3), [1, 2, 3, 4]), $this->equalTo([1, 2]));
  }

  public function test_drop_should_return_a_new_array_with_the_first_X_elements_removed()
  {
    $this->assertThat(Collection::drop(3, [1, 2, 3, 4]), $this->equalTo([4]));
  }

  public function test_dropWhile_should_return_a_new_array_where_the_first_X_elements_satisfying_the_predicate_are_removed()
  {
    $this->assertThat(Collection::dropWhile(Relational::lessThan(Core::_, 3), [1, 2, 3, 4]), $this->equalTo([3, 4]));
  }

  public function test_zipWith_returns_a_new_array_of_the_result_of_a_callable_applied_to_every_element_of_two_arrays()
  {
    $this->assertThat(Collection::zipWith('P\Arithmetic::add', [1, 2, 3, 4], [4, 3, 2, 1]), $this->equalTo([5, 5, 5, 5]));
  }

}
