<?php

require_once 'vendor/autoload.php';

use App\Container;
use App\Example\Shop;

$container = new Container;

var_dump($container->resolve(Shop::class));
