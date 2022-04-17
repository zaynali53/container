<?php

namespace App;

use Closure;
use ReflectionClass;
use ReflectionException;
use App\Exception\BindingException;

class Container {
  protected $bindings = [];

  protected $shared = [];

  public function bind($abstract, $concrete, $shared = false) {
    $this->bindings[$abstract] = [
      'concrete' => $concrete,
      'shared' => $shared,
    ];
  }

  public function singleton($abstract, $concrete) {
    $this->bind($abstract, $concrete, true);
  }

  protected function make($concrete) {
    try {
      $reflector = new ReflectionClass($concrete);
    }
    catch(ReflectionException $e) {
      throw new BindingException("Target class [$concrete] does not exist.");
    }

    if ( ! $reflector->isInstantiable()) {
      throw new BindingException("Target class [$concrete] is not instantiable.");
    }

    $constructor = $reflector->getConstructor();

    if (is_null($constructor)) {
      return $reflector->newInstance();
    }

    $parameters = $constructor->getParameters();

    $dependencies = [];

    foreach($parameters as $parameter) {
      $dependencies[] = $this->make($parameter->getType()->getName());
    }

    return $reflector->newInstanceArgs($dependencies);
  }

  public function resolve($abstract) {
    if (isset($this->shared[$abstract])) {
      return $this->shared[$abstract];
    }

    if (class_exists($abstract)) {
      return $this->make($abstract);
    }

    $concrete = $this->bindings[$abstract]['concrete'];
    $shared   = $this->bindings[$abstract]['shared'];

    if ($concrete instanceof Closure) {
      $concrete = $concrete();
    }

    if (class_exists($concrete)) {
      $concrete = $this->make($concrete);
    }

    if ($shared) {
      $this->shared[$abstract] = $concrete;
    }

    return $concrete;
  }
}