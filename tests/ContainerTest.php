<?php

namespace Test;

use App\Container;
use App\Example\Shop;
use App\Example\Product;
use App\Example\AbstractTest;
use App\Example\InterfaceTest;
use App\Example\NotFoundNestedTest;
use App\Example\NotFoundTest;
use App\Exception\BindingException;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase {

  /** @test */
  public function it_can_resolve_values() {
    $container = new Container;

    $container->bind('foo', 'bar');

    $this->assertEquals('bar', $container->resolve('foo'));
  }

  /** @test */
  public function it_can_resolve_closures() {
    $container = new Container;

    $container->bind('foo', function() {
      return 'bar';
    });

    $this->assertEquals('bar', $container->resolve('foo'));
  }

  /** @test */
  public function it_can_resolve_singleton() {
    $container = new Container;

    $container->singleton('foo', Product::class);

    $first = $container->resolve('foo');
    $second = $container->resolve('foo');

    $this->assertSame($first, $second);
  }

  /** @test */
  public function it_can_auto_resolve_single_class() {
    $container = new Container;

    $this->assertInstanceOf(
      Product::class, 
      $container->resolve(Product::class)
    );
  }

  /** @test */
  public function it_can_auto_resolve_nested_class() {
    $container = new Container;

    $this->assertInstanceOf(
      Shop::class, 
      $container->resolve(Shop::class)
    );
  }

  /** @test */
  public function throws_if_concrete_not_found() {
    $container = new Container;

    $this->expectException(BindingException::class);

    $container->resolve(NotFoundTest::class);
  }

  /** @test */
  public function throws_if_nested_concrete_not_found() {
    $container = new Container;

    $this->expectException(BindingException::class);

    $container->resolve(NotFoundNestedTest::class);
  }

  /** @test */
  public function throws_if_concrete_cannot_be_instantiated() {
    $container = new Container;

    $this->expectException(BindingException::class);

    $container->resolve(AbstractTest::class);
    $container->resolve(InterfaceTest::class);
  }
}