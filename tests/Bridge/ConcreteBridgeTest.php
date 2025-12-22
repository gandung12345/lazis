<?php

declare(strict_types=1);

namespace Schnell\Tests\Bridge;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[CoversClass(ConcreteBridge::class)]
class ConcreteBridgeTest extends TestCase
{
    public function testCanGetInstance()
    {
        $bridge = new ConcreteBridge();
        $this->assertInstanceOf(ConcreteBridge::class, $bridge);
    }

    public function testGetConfigIsNull()
    {
        $bridge = new ConcreteBridge();
        $this->assertInstanceOf(ConcreteBridge::class, $bridge);
        $this->assertNull($bridge->getConfig());
    }

    public function testGetContainerIsNull()
    {
        $bridge = new ConcreteBridge();
        $this->assertInstanceOf(ConcreteBridge::class, $bridge);
        $this->assertNull($bridge->getContainer());
    }

    public function testGetBasePathIsNull()
    {
        $bridge = new ConcreteBridge();
        $this->assertInstanceOf(ConcreteBridge::class, $bridge);
        $this->assertNull($bridge->getBasePath());
    }
}
