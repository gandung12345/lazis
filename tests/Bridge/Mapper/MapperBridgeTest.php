<?php

declare(strict_types=1);

namespace Schnell\Tests\Bridge\Mapper;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
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
use Schnell\Config\Node\AbstractNode;
use Schnell\Config\Node\Arr;
use Schnell\Config\Node\Assign;
use Schnell\Config\Node\Block;
use Schnell\Config\Node\Boolean;
use Schnell\Config\Node\Identifier;
use Schnell\Config\Node\Integer;
use Schnell\Config\Node\Str;
use Schnell\Config\Parser;
use Schnell\Container;
use Schnell\Bridge\Doctrine\DoctrineBridge;
use Schnell\Bridge\Mapper\MapperBridge;
use Schnell\Exception\ExtensionException;
use Schnell\Mapper\Mapper;
use Schnell\Mapper\MapperInterface;
use Schnell\Tests\Bridge\AbstractBridgeTestCase;

use function getcwd;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[CoversClass(MapperBridge::class)]
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
#[CoversClass(AbstractNode::class)]
#[CoversClass(Arr::class)]
#[CoversClass(Assign::class)]
#[CoversClass(Block::class)]
#[CoversClass(Boolean::class)]
#[CoversClass(Identifier::class)]
#[CoversClass(Integer::class)]
#[CoversClass(Str::class)]
#[CoversClass(Parser::class)]
#[CoversClass(Container::class)]
#[CoversClass(DoctrineBridge::class)]
#[CoversClass(Mapper::class)]
class MapperBridgeTest extends AbstractBridgeTestCase
{
    public function testCanGetInstance()
    {
        $bridge = new MapperBridge(
            $this->getConfig(),
            $this->getContainer(),
            getcwd()
        );

        $this->assertInstanceOf(MapperBridge::class, $bridge);
    }

    public function testCanThrowExceptionWhenCallLoadMethod()
    {
        $this->expectException(ExtensionException::class);

        $bridge = new MapperBridge(
            $this->getConfig(),
            $this->getContainer(),
            getcwd(),
        );

        $bridge->load();
    }

    public function testCanCallLoadMethodWithoutFault()
    {
        $doctrineBridge = new DoctrineBridge(
            $this->getConfig(),
            $this->getContainer(),
            getcwd()
        );

        $mapperBridge = new MapperBridge(
            $this->getConfig(),
            $this->getContainer(),
            getcwd()
        );

        $doctrineBridge->load();
        $mapperBridge->load();

        $this->assertInstanceOf(
            EntityManagerInterface::class,
            $this->getContainer()->get('entity-manager')
        );

        $this->assertInstanceOf(
            MapperInterface::class,
            $this->getContainer()->get('mapper')
        );
    }
}
