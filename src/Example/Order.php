<?php

namespace App\Example;

class Order {
  public function __construct(public Product $product) {}
}