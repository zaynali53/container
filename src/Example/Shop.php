<?php

namespace App\Example;

class Shop {
  public function __construct(public Order $order) {}
}