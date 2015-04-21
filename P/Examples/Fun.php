<?php
use P\Arithmetic;
use P\Bitwise;
use P\Collection;
use P\Core;
use P\Maybe\Just;
use P\Maybe\Nothing;
use P\Relational;

require __DIR__ . '../../../vendor/autoload.php';

$Sum = Collection::foldl('\P\Arithmetic::add', 0);
$Prod = Collection::foldl('\P\Arithmetic::multiply', 1);

var_dump($Sum([1, 2, 3, 4, 5]));
var_dump($Prod([1, 2, 3, 4, 5]));

$fibonacci = Core::fix(function($partial) {
  return Core::conditions([
    [Relational::lessOrEqualTo(Core::_, 1), 'P\Core::identity'],
    [Core::constant(true),                  function($n) use ($partial) { return $partial($n - 1) + $partial($n - 2); }]
  ]);
});

var_dump($fibonacci(10));
