<?php

declare(strict_types=1);

namespace Schnell\Tests\Config\Ast\Node;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[CoversClass(ConcreteAstNode::class)]
class AbstractNodeTest extends TestCase
{
    public function testCanGetInstance()
    {
        $concrete = new ConcreteAstNode(31337);
        $this->assertInstanceOf(ConcreteAstNode::class, $concrete);
    }

    public function testCanGetNameThroughToStringMethod()
    {
        $concrete = new ConcreteAstNode(31337);
        $this->assertEquals('(concrete)', $concrete);
    }
}
