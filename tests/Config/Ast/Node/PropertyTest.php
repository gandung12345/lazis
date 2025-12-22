<?php

declare(strict_types=1);

namespace Schnell\Tests\Config\Ast\Node;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Schnell\Config\Ast\Node\Property;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[CoversClass(Property::class)]
class PropertyTest extends TestCase
{
    public function testCanGetName()
    {
        $property = new Property('foobar', []);
        $this->assertEquals('(property)', $property->getName());
    }
}
