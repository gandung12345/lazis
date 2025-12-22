<?php

declare(strict_types=1);

namespace Schnell\Tests\Config\Ast;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Schnell\Config\Ast\Ast;
use Schnell\Config\Ast\AstInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[CoversClass(Ast::class)]
class AstTest extends TestCase
{
    public function testCanGetChildsAtWithNullResult()
    {
        $ast = new Ast(31337, null);
        $this->assertInstanceOf(AstInterface::class, $ast);
        $this->assertNull($ast->getChildAt(0));
    }

    public function testCanVisitWithNullResult()
    {
        $ast = new Ast(31337, null);
        $this->assertInstanceOf(AstInterface::class, $ast);
        $this->assertNull($ast->visit());
    }
}
