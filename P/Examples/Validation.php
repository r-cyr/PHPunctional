<?php
require __DIR__ . '../../../vendor/autoload.php';

use P\Core;
use P\Validation\Validation;
use P\Validation\Success;
use P\Validation\Failure;

function notEmpty($prop, $str) {
  return isset($str) && strlen($str) !== 0 ? Success::of($str)
                                           : Failure::of(["$prop is empty"]);
}

function minLength($prop, $str, $n) {
  return strlen($str) >= $n ? Success::of($str)
                            : Failure::of(["$prop is shorter than $n characters"]);
}

function maxLength($prop, $str, $n) {
  return strlen($str) <= $n ? Success::of($str)
                            : Failure::of(["$prop is longer than $n characters"]);
}

function containsAtLeastOneNumber($prop, $str) {
  return preg_match('/[0-9]+/', $str) ? Success::of($str)
                                      : Failure::of(["$prop should contain at least one number"]);
}

function containsAtLeastOneCapitalLetter($prop, $str) {
  return preg_match('/[A-Z]+/', $str) ? Success::of($str)
                                      : Failure::of(["$prop should contain at least one capital letter"]);
}

function isValidUsername($username) {
  return Validation::of($username)
    ->seql(notEmpty("Username", $username))
    ->seql(minLength("Username", $username, 5))
    ->seql(maxLength("Username", $username, 50));
}

function isValidPassword($pass) {
  return Validation::of($pass)
    ->seql(notEmpty("Password", $pass))
    ->seql(minLength("Password", $pass, 5))
    ->seql(maxLength("Password", $pass, 50))
    ->seql(containsAtLeastOneCapitalLetter("Password", $pass))
    ->seql(containsAtLeastOneNumber("Password", $pass));
}

class User {
  private $username;
  private $password;

  public function __construct($username, $password)
  {
    $this->username = $username;
    $this->password = $password;
  }

  public function getUsername()
  {
    return $this->username;
  }

  public function getPassword()
  {
    return $this->password;
  }
}

// Using Applicative style: `ap` (<*>)
$person = Validation::of(P\Core::construct('User'))
  ->ap(isValidUsername("Testing"))
  ->ap(isValidPassword("")->map('sha1'))
  ->getOrElse(function($failures) { return $failures; });

var_dump($person);

// or using liftA2
$person = Core::liftA2(
  P\Core::construct('User'),
  isValidUsername("Testing"),
  isValidPassword("Abc123")->map('sha1')
)->getOrElse(function($failures) { return $failures; });

var_dump($person);
