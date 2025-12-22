<?php

declare(strict_types=1);

namespace Schnell\Tests\Config\Ast\Node;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Schnell\Config\Ast\Node\Block;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[CoversClass(Block::class)]
class BlockTest extends TestCase
{
    public function testCanGetName()
    {
        $block = new Block('foobar');
        $this->assertEquals('(block)', $block->getName());
    }
}
