<?php

declare(strict_types=1);

namespace Schnell\Config;

use Schnell\Config\Ast\Ast;
use Schnell\Config\Ast\AstInterface;
use Schnell\Config\Node\NodeInterface;
use Schnell\Config\Node\NodeTypes;
use Schnell\Config\Ast\Node\Block as AstBlockNode;
use Schnell\Config\Ast\Node\Property as AstPropertyNode;
use Schnell\Config\Ast\Node\Root as AstRootNode;
use Schnell\Config\Ast\Node\NodeTypes as AstNodeTypes;
use Schnell\Config\Ast\Visitor\Block as AstBlockVisitor;
use Schnell\Config\Ast\Visitor\Property as AstPropertyVisitor;
use Schnell\Exception\ConfigParserException;

use function class_exists;

// help opcache.preload discover always-needed symbols
// @codeCoverageIgnoreStart
// phpcs:disable
class_exists(Ast::class);
class_exists(AstBlockNode::class);
class_exists(AstPropertyNode::class);
class_exists(AstRootNode::class);
class_exists(AstNodeTypes::class);
class_exists(AstBlockVisitor::class);
class_exists(AstPropertyVisitor::class);
class_exists(ConfigParserException::class);
// phpcs:enable
// @codeCoverageIgnoreEnd

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Parser implements ParserInterface
{
    /**
     * @var array
     */
    private $tokens;

    /**
     * @var \Schnell\Config\Node\NodeInterface|null
     */
    private $token;

    /**
     * @var int
     */
    private $position;

    /**
     * @var \Schnell\Config\Ast\AstInterface
     */
    private $root;

    /**
     * @psalm-api
     *
     * @param array $tokens
     * @return static
     */
    public function __construct(array $tokens)
    {
        $this->initialize(
            $tokens,
            null,
            0,
            new Ast(new AstRootNode(), null)
        );
    }

    /**
     * @psalm-api
     * @psalm-suppress UnusedParam
     *
     * @param array $tokens
     * @param \Schnell\Config\Node\NodeInterface|null $token
     * @param int $position
     * @param \Schnell\Config\Ast\AstInterface $root
     * @return void
     */
    private function initialize(
        array $tokens,
        NodeInterface|null $token,
        int $position,
        AstInterface $root
    ): void {
        $this->setTokens($tokens);
        $this->setToken($token);
        $this->setPosition($position);
        $this->setRoot($root);
    }

    /**
     * {@inheritdoc}
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokensAt(int $index): NodeInterface|null
    {
        if (!isset($this->tokens[$index])) {
            return null;
        }

        return $this->tokens[$index];
    }

    /**
     * {@inheritdoc}
     */
    public function setTokens(array $tokens): void
    {
        $this->tokens = $tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(): NodeInterface|null
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(NodeInterface|null $token): void
    {
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function resetToken(): void
    {
        $this->setToken(null);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementPosition(): void
    {
        $this->position++;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot(): AstInterface
    {
        return $this->root;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoot(AstInterface $root): void
    {
        $this->root = $root;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(): void
    {
        while (true) {
            if ($this->isEot()) {
                break;
            }

            if ($this->isBlock()) {
                $this->processBlock();
                $this->resetToken();
            }

            if ($this->isIdentifier()) {
                $this->processProperty();
                $this->resetToken();
            }

            $this->persist();
            $this->next();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function ast(): AstInterface
    {
        return $this->getRoot();
    }

    /**
     * @return void
     */
    private function processBlock(): void
    {
        /** @psalm-suppress PossiblyNullReference */
        $val = new AstBlockNode($this->current()->getValue());
        $ast = new Ast($val, null);

        $ast->setVisitor(new AstBlockVisitor($ast));
        $this->getRoot()->addChild($ast);
    }

    /**
     * @return void
     */
    private function processProperty(): void
    {
        if ($this->isEot()) {
            $prev = $this->revert(1);

            /** @psalm-suppress PossiblyNullReference */
            throw new ConfigParserException(
                sprintf(
                    "Line %d, column %d: property identifier must not " .
                    "ended with itself. Got an EOF.",
                    $prev->getLineNumber(),
                    $prev->getColumnNumber()
                )
            );
        }

        if (sizeof($this->getRoot()->getChilds()) === 0) {
            $prev = $this->revert(1);

            /** @psalm-suppress PossiblyNullReference */
            throw new ConfigParserException(
                sprintf(
                    "Line %d, column %d: block property cannot " .
                    "be unblocked.",
                    $prev->getLineNumber(),
                    $prev->getColumnNumber()
                )
            );
        }

        $result = [$this->current()];

        $this->persist();

        /** @psalm-suppress PossiblyNullReference */
        if ($this->current()->getType() !== NodeTypes::ASSIGN) {
            $prev = $this->revert(1);

            throw new ConfigParserException(
                sprintf(
                    "Line %d, column %d: property identifier must be " .
                    "followed by an assignment operator.",
                    $prev->getLineNumber(),
                    $prev->getColumnNumber()
                )
            );
        }

        $result[] = $this->current();

        $this->next();

        if ($this->isEot()) {
            $prev = $this->revert(1);

            /** @psalm-suppress PossiblyNullReference */
            throw new ConfigParserException(
                sprintf(
                    "Line %d, column %d: assignment operator must be " .
                    "followed by literal value.",
                    $prev->getLineNumber(),
                    $prev->getColumnNumber()
                )
            );
        }

        $this->persist();

        /** @psalm-suppress PossiblyNullReference */
        if (
            $this->current()->getType() !== NodeTypes::BOOLEAN &&
            $this->current()->getType() !== NodeTypes::INTEGER &&
            $this->current()->getType() !== NodeTypes::STRING &&
            $this->current()->getType() !== NodeTypes::ARRAY
        ) {
            throw new ConfigParserException(
                sprintf(
                    "Line %d, column %d: assignment operator must be " .
                    "followed by string, integer or array literal.",
                    $this->current()->getLineNumber(),
                    $this->current()->getColumnNumber()
                )
            );
        }

        $result[] = $this->current();

        /** @psalm-suppress PossiblyNullReference */
        $node = new AstPropertyNode(
            $result[0]->getValue(),
            $result[2]->getValue()
        );
        $tmp = new Ast($node, null);
        $tmp->setVisitor(new AstPropertyVisitor($tmp));

        /** @psalm-suppress PossiblyNullReference */
        $this->getRoot()->getLastChild()->addChild($tmp);
    }

    /**
     * @return \Schnell\Config\Node\NodeInterface|null
     */
    private function current(): NodeInterface|null
    {
        return $this->getToken();
    }

    /**
     * @return void
     */
    private function persist(): void
    {
        $this->setToken($this->getTokensAt($this->getPosition()));
    }

    /**
     * @return void
     */
    private function next(): void
    {
        $this->incrementPosition();
    }

    /**
     * @psalm-api
     * @psalm-suppress PossiblyUnusedParam
     *
     * @param int $depth
     * @return \Schnell\Config\Node\NodeInterface|null
     */
    private function peek(int $depth): NodeInterface|null
    {
        if ($this->getPosition() + $depth >= sizeof($this->getTokens())) {
            return null;
        }

        return $this->getTokensAt($this->getPosition() + $depth);
    }

    /**
     * @psalm-api
     * @psalm-suppress PossiblyUnusedParam
     *
     * @param int $depth
     * @return \Schnell\Config\Node\NodeInterface|null
     */
    private function revert(int $depth): NodeInterface|null
    {
        if ($this->getPosition() - $depth < 0) {
            return null;
        }

        return $this->getTokensAt($this->getPosition() - $depth);
    }

    /**
     * @return bool
     */
    private function isBlock(): bool
    {
        if ($this->current() === null) {
            return false;
        }

        /** @psalm-suppress PossiblyNullReference */
        return $this->current()->getType() === NodeTypes::BLOCK;
    }

    /**
     * @return bool
     */
    private function isIdentifier(): bool
    {
        if ($this->current() === null) {
            return false;
        }

        /** @psalm-suppress PossiblyNullReference */
        return $this->current()->getType() === NodeTypes::IDENTIFIER;
    }

    /**
     * @return bool
     */
    private function isEot(): bool
    {
        return $this->getPosition() >= sizeof($this->getTokens());
    }
}
