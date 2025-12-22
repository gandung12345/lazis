<?php

declare(strict_types=1);

namespace Schnell\Tests\Bridge\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Schnell\Bridge\Doctrine\DoctrineBridge;
use Schnell\Config\Ast\Ast;
use Schnell\Config\Ast\Node\AbstractNode as AstAbstractNode;
use Schnell\Config\Ast\Node\Block as AstBlockNode;
use Schnell\Config\Ast\Node\Property as AstPropertyNode;
use Schnell\Config\Ast\Node\Root as AstRootNode;
use Schnell\Config\Ast\Visitor\AbstractVisitor as AstAbstractVisitor;
use Schnell\Config\Ast\Visitor\Block as AstBlockVisitor;
use Schnell\Config\Ast\Visitor\Property as AstPropertyVisitor;
use Schnell\Config\Config;
use Schnell\Config\Lexer;
use Schnell\Config\Parser;
use Schnell\Config\Node\AbstractNode;
use Schnell\Config\Node\Arr as ArrNode;
use Schnell\Config\Node\Assign as AssignNode;
use Schnell\Config\Node\Block as BlockNode;
use Schnell\Config\Node\Boolean as BooleanNode;
use Schnell\Config\Node\Identifier as IdentifierNode;
use Schnell\Config\Node\Integer as IntegerNode;
use Schnell\Config\Node\Str as StrNode;
use Schnell\Container;
use Schnell\Tests\Bridge\AbstractBridgeTestCase;

use function getcwd;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[CoversClass(DoctrineBridge::class)]
#[CoversClass(Ast::class)]
#[CoversClass(AstAbstractNode::class)]
#[CoversClass(AstBlockNode::class)]
#[CoversClass(AstPropertyNode::class)]
#[CoversClass(AstRootNode::class)]
#[CoversClass(AstAbstractVisitor::class)]
#[CoversClass(AstBlockVisitor::class)]
#[CoversClass(AstPropertyVisitor::class)]
#[CoversClass(Config::class)]
#[CoversClass(Lexer::class)]
#[CoversClass(Parser::class)]
#[CoversClass(AbstractNode::class)]
#[CoversClass(ArrNode::class)]
#[CoversClass(AssignNode::class)]
#[CoversClass(BlockNode::class)]
#[CoversClass(BooleanNode::class)]
#[CoversClass(IdentifierNode::class)]
#[CoversClass(IntegerNode::class)]
#[CoversClass(StrNode::class)]
#[CoversClass(Container::class)]
class DoctrineBridgeTest extends AbstractBridgeTestCase
{
    public function testCanGetInstance()
    {
        $bridge = new DoctrineBridge(
            $this->getConfig(),
            $this->getContainer(),
            getcwd()
        );

        $this->assertInstanceOf(DoctrineBridge::class, $bridge);
    }

    public function testCanCallLoadAndEnsureEntityManagerIsExist()
    {
        $bridge = new DoctrineBridge(
            $this->getConfig(),
            $this->getContainer(),
            getcwd()
        );

        $bridge->load();

        $this->assertInstanceOf(
            EntityManagerInterface::class,
            $this->getContainer()->get('entity-manager')
        );
    }

    public function testCanCallLoadWithAnotherConfig()
    {
        $bridge = new DoctrineBridge(
            $this->getAnotherConfig(),
            $this->getContainer(),
            getcwd()
        );

        $bridge->load();

        $this->assertInstanceOf(
            EntityManagerInterface::class,
            $this->getContainer()->get('entity-manager')
        );
    }
}
