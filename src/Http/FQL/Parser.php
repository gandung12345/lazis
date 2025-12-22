<?php

declare(strict_types=1);

namespace Schnell\Http\FQL;

use ReflectionClass;
use ReflectionProperty;
use Doctrine\ORM\QueryBuilder;
use Schnell\Entity\EntityInterface;
use Schnell\Exception\FQLParserException;
use Schnell\Http\FQL\Ast\Ast;
use Schnell\Http\FQL\Ast\AstInterface;
use Schnell\Http\FQL\Ast\Node\ExprNodeInterface as AstExprNodeInterface;
use Schnell\Http\FQL\Ast\Node\Root as AstRootNode;
use Schnell\Http\FQL\Ast\Node\Literal\KeyValue as AstKeyValueLiteral;
use Schnell\Http\FQL\Ast\Node\Expr\ExprAndX as AstExprAndX;
use Schnell\Http\FQL\Ast\Node\Expr\ExprEqualX as AstExprEqualX;
use Schnell\Http\FQL\Ast\Node\Expr\ExprGreaterOrEqualX as AstExprGreaterOrEqualX;
use Schnell\Http\FQL\Ast\Node\Expr\ExprGreaterX as AstExprGreaterX;
use Schnell\Http\FQL\Ast\Node\Expr\ExprLessOrEqualX as AstExprLessOrEqualX;
use Schnell\Http\FQL\Ast\Node\Expr\ExprLessX as AstExprLessX;
use Schnell\Http\FQL\Ast\Node\Expr\ExprLikeX as AstExprLikeX;
use Schnell\Http\FQL\Ast\Node\Expr\ExprNotEqualX as AstExprNotEqualX;
use Schnell\Http\FQL\Ast\Node\Expr\ExprOrX as AstExprOrX;
use Schnell\Http\FQL\Node\ExprNodeInterface;
use Schnell\Http\FQL\Node\NodeInterface;
use Schnell\Http\FQL\Node\NodeTypes;

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Parser implements ParserInterface
{
    /**
     * @var array|null
     */
    private ?array $tokens;

    /**
     * @var \Doctrine\ORM\QueryBuilder|null
     */
    private ?QueryBuilder $queryBuilder;

    /**
     * @var \Schnell\Entity\EntityInterface|null
     */
    private ?EntityInterface $entity;

    /**
     * @var int
     */
    private int $position;

    /**
     * @var bool|null
     */
    private ?bool $scoped;

    /**
     * @var int|null
     */
    private ?int $scopeCount;

    /**
     * @var \Schnell\Http\FQL\Ast\AstInterface|null
     */
    private ?AstInterface $ast;

    /**
     * @var \Schnell\Http\FQL\ParameterBag|null
     */
    private ?ParameterBag $parameterBag;

    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var array<\Schnell\Http\FQL\Ast\AstInterface>
     */
    private array $scopeContext = [];

    /**
     * @psalm-api
     *
     * @param array|null $tokens
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder
     * @param \Schnell\Entity\EntityInterface|null $entity
     * @param \Schnell\Http\FQL\Ast\AstInterface|null $ast
     * @param \Schnell\Http\FQL\ParameterBag|null $parameterBag
     * @param bool|null $scoped
     * @param int|null $scopeCount
     * @return static
     */
    public function __construct(
        ?array $tokens = null,
        ?QueryBuilder $queryBuilder = null,
        ?EntityInterface $entity = null,
        ?AstInterface $ast = null,
        ?ParameterBag $parameterBag = null,
        ?bool $scoped = false,
        ?int $scopeCount = 0
    ) {
        $this->setTokens($tokens);
        $this->setQueryBuilder($queryBuilder);
        $this->setEntity($entity);
        $this->setParameterBag($parameterBag ?? new ParameterBag());
        $this->setAst($ast ?? $this->buildAstRootNode());
        $this->setPosition(-1);
        $this->setScoped($scoped);
        $this->setScopeCount($scopeCount);
    }

    /**
     * {@inheritDoc}
     */
    public function getTokens(): ?array
    {
        return $this->tokens;
    }

    /**
     * {@inheritDoc}
     */
    public function setTokens(?array $tokens): void
    {
        $this->tokens = $tokens;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilder(): ?QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function setQueryBuilder(?QueryBuilder $queryBuilder): void
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity(): ?EntityInterface
    {
        return $this->entity;
    }

    /**
     * {@inheritDoc}
     */
    public function setEntity(?EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * {@inheritDoc}
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * {@inheritDoc}
     */
    public function getScoped(): ?bool
    {
        return $this->scoped;
    }

    /**
     * {@inheritDoc}
     */
    public function setScoped(?bool $scoped): void
    {
        $this->scoped = $scoped;
    }

    /**
     * {@inheritDoc}
     */
    public function getScopeCount(): ?int
    {
        return $this->scopeCount;
    }

    /**
     * {@inheritDoc}
     */
    public function setScopeCount(?int $scopeCount): void
    {
        $this->scopeCount = $scopeCount;
    }

    /**
     * {@inheritDoc}
     */
    public function decrementScopeCount(): void
    {
        $this->scopeCount--;
    }

    /**
     * {@inheritDoc}
     */
    public function incrementScopeCount(): void
    {
        $this->scopeCount++;
    }

    /**
     * {@inheritDoc}
     */
    public function getAst(): ?AstInterface
    {
        return $this->ast;
    }

    /**
     * {@inheritDoc}
     */
    public function setAst(?AstInterface $ast): void
    {
        $this->ast = $ast;
    }

    /**
     * {@inheritDoc}
     */
    public function getParameterBag(): ?ParameterBag
    {
        return $this->parameterBag;
    }

    /**
     * {@inheritDoc}
     */
    public function setParameterBag(?ParameterBag $parameterBag): void
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @return \Schnell\Http\FQL\Ast\AstInterface
     */
    private function buildAstRootNode(): AstInterface
    {
        return new Ast(
            new AstRootNode(),
            null,
            $this->getQueryBuilder(),
            $this->getParameterBag()
        );
    }

    /**
     * @return bool
     */
    private function isEot(): bool
    {
        return $this->getPosition() >= sizeof($this->getTokens());
    }

    /**
     * @param int $depth
     * @return \Schnell\Http\FQL\Node\NodeInterface|null
     */
    private function peek(int $depth = 1): ?NodeInterface
    {
        if ($this->getPosition() + $depth >= sizeof($this->getTokens())) {
            return null;
        }

        return $this->tokens[$this->getPosition() + $depth];
    }

    /**
     * @return void
     */
    private function next(): void
    {
        $this->position++;
    }

    /**
     * @return void
     */
    private function processScope(): void
    {
        if (!$this->getScoped()) {
            $this->setScoped(true);
        }

        $this->incrementScopeCount();
    }

    /**
     * @return void
     */
    private function processUnscope(): void
    {
        $this->decrementScopeCount();

        if ($this->getScopeCount() === 0) {
            $this->setScoped(false);
        }
    }

    /**
     * @return \Schnell\Http\FQL\Node\NodeInterface|null
     */
    private function current(): ?NodeInterface
    {
        if ($this->isEot()) {
            return null;
        }

        return $this->tokens[$this->getPosition()];
    }

    /**
     * @param \Schnell\Http\FQL\Node\ExprNodeInterface
     * @return \Schnell\Http\FQL\Ast\Node\ExprNodeInterface|null
     */
    private function getExprNode(?ExprNodeInterface $node): ?AstExprNodeInterface
    {
        if (null === $node) {
            return null;
        }

        return match ($node->getType()) {
            NodeTypes::ANDX => new AstExprAndX(),
            NodeTypes::EQUALX => new AstExprEqualX(),
            NodeTypes::GREATER_OR_EQUALX => new AstExprGreaterOrEqualX(),
            NodeTypes::GREATERX => new AstExprGreaterX(),
            NodeTypes::LESS_OR_EQUALX => new AstExprLessOrEqualX(),
            NodeTypes::LESSX => new AstExprLessX(),
            NodeTypes::LIKEX => new AstExprLikeX(),
            NodeTypes::NOT_EQUALX => new AstExprNotEqualX(),
            NodeTypes::ORX => new AstExprOrX(),
            default => null
        };
    }

    /**
     * @param string $name
     * @return string
     */
    private function resolveFilterColumn(string $name): string
    {
        $reflection = new ReflectionClass($this->getEntity());
        $properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);
        $resolved = '';

        foreach ($properties as $property) {
            $jsonAttr = $property->getAttributes(\Schnell\Attribute\Schema\Json::class);

            if (sizeof($jsonAttr) === 0) {
                continue;
            }

            if ($jsonAttr[0]->newInstance()->getName() === $name) {
                $resolved = $property->getName();
                break;
            }
        }

        return $resolved;
    }

    /**
     * @return void
     */
    private function checkForWildEndscope(): void
    {
        if (null === $this->peek()) {
            return;
        }

        $count = 0;

        while (null !== ($obj = $this->peek())) {
            if ($obj->getType() !== NodeTypes::CLOSE_SQUARE_BRACE) {
                $this->next();
                continue;
            }

            $this->next();
            $count++;
        }

        throw new FQLParserException(
            sprintf(
                "Invalid FQL expression: There was a %d of unclaimed close " .
                "square brace after completed scope.",
                $count
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function parse(): void
    {
        $this->pushScopeContext($this->getAst());
        $this->parseScopedExpr();
        $this->checkForWildEndscope();
    }

    /**
     * @internal
     * @return void
     */
    private function parseScopedExpr(): void
    {
        if ($this->isEot()) {
            return;
        }

        $this->expectNode(NodeTypes::OPEN_SQUARE_BRACE, "Missing open square brace");
        $this->next();
        $this->processScope();

        $this->parseExprOrMapClause();

        while (1) {
            $obj = $this->peek(1);

            if (null === $obj || $obj->getType() !== NodeTypes::COMMA) {
                break;
            }

            $this->next();
            $this->parseExprOrMapClause();
        }

        $this->expectNode(NodeTypes::CLOSE_SQUARE_BRACE, "Missing close square brace");
        $this->next();
        $this->processUnscope();

        // end of scope, pull the last scope context.
        $this->pullScopeContext();
    }

    /**
     * @internal
     * @return void
     */
    private function parseExprOrMapClause(): void
    {
        $this->expect(
            null !== ($obj = $this->peek(1)),
            "Invalid expression: next token after open square brace must not null."
        );

        if ($obj instanceof ExprNodeInterface) {
            $this->parseRawExpr();
            return;
        }

        $this->parseMapExpr();
    }

    /**
     * @internal
     * @return void
     */
    private function parseRawExpr(): void
    {
        $this->next();

        $ast = new Ast();
        $ast->setValue($this->getExprNode($this->current()));
        $ast->setQueryBuilder($this->getQueryBuilder());
        $ast->setParameterBag($this->getParameterBag());

        $context = $this->pullScopeContext();
        $context->addChild($ast);

        $ast->setParent($context);

        $this->pushScopeContext($context);
        $this->pushScopeContext($ast);

        $this->expectNode(NodeTypes::COLON, "Missing colon.");
        $this->next();

        $this->parseScopedExpr();
    }

    /**
     * @internal
     * @return void
     */
    private function parseMapExpr(): void
    {
        $this->next();

        $key = $this->current();

        $this->expectNode(NodeTypes::COLON, "Missing colon.");
        $this->next();
        $this->next();

        $value = $this->current();
        $kval = new AstKeyValueLiteral(
            $this->resolveFilterColumn($key->getValue()),
            $value->getValue()
        );

        $ast = new Ast();
        $ast->setValue($kval);
        $ast->setQueryBuilder($this->getQueryBuilder());
        $ast->setParameterBag($this->getParameterBag());

        $context = $this->pullScopeContext();
        $context->addChild($ast);

        $ast->setParent($context);

        $this->pushScopeContext($context);
    }

    /**
     * @internal
     * @return void
     */
    private function pushScopeContext(AstInterface $ast): void
    {
        $this->scopeContext[] = $ast;
    }

    /**
     * @internal
     * @return void
     */
    private function pullScopeContext(): AstInterface
    {
        return array_pop($this->scopeContext);
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress UnusedParam
     *
     * @param bool $condition
     * @param string $exceptionMessage
     * @return void
     */
    private function expect(bool $condition, string $exceptionMessage): void
    {
        if (!$condition) {
            throw new FQLParserException($exceptionMessage);
        }

        return;
    }

    /**
     * @param int $type
     * @param string $exceptionMessage
     * @return void
     */
    private function expectNode(int $type, string $exceptionMessage): void
    {
        $this->expect($this->peek() !== null, $exceptionMessage);
        /** @psalm-suppress PossiblyNullReference */
        $this->expect($this->peek()->getType() === $type, $exceptionMessage);
        return;
    }

    /**
     * @param array $types
     * @param string $exceptionMessage
     * @return void
     */
    private function expectNodes(array $types, string $exceptionMessage): void
    {
        $this->expect(null === $this->peek(), $exceptionMessage);

        $found = false;

        foreach ($types as $type) {
            /** @psalm-suppress PossiblyNullReference */
            if ($this->peek()->getType() === $type) {
                $found = true;
                break;
            }
        }

        $this->expect(!$found, $exceptionMessage);
        return;
    }
}
