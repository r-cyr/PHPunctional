<?php

namespace P\Test\Fixture;

class Point {
  private $x;
  private $y;

  public function __construct($x = 0, $y = 0)
  {
    $this->x = $x;
    $this->y = $y;
  }

  public function x()
  {
    return $this->x;
  }

  public function y()
  {
    return $this->y;
  }
}