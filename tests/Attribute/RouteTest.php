<?php

declare(strict_types=1);

namespace Schnell\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Schnell\Attribute\Route;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[CoversClass(Route::class)]
class RouteTest extends TestCase
{
    public function testCanGetInstance(): void
    {
        $route = new Route('/foobar', 'GET');
        $this->assertInstanceOf(Route::class, $route);
    }

    public function testCanGetUrl(): void
    {
        $route = new Route('/foobar', 'GET');
        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('/foobar', $route->getUrl());
    }

    public function testCanGetMethod(): void
    {
        $route = new Route('/foobar', 'GET');
        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('GET', $route->getMethod());
    }

    public function testCanGetIdentifier(): void
    {
        $route = new Route('/foobar', 'GET');
        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('route', $route->getIdentifier());
    }
}
