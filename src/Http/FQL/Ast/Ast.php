<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Ast;

use Doctrine\ORM\QueryBuilder;
use Schnell\Exception\FQLParserException;
use Schnell\Http\FQL\ParameterBag;
use Schnell\Http\FQL\Ast\Node\NodeInterface as AstNodeInterface;
use Schnell\Http\FQL\Ast\Node\NodeTypes;

use function call_user_func_array;

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Ast implements AstInterface
{
    /**
     * @var \Schnell\Http\FQL\Ast\Node\NodeInterface|null
     */
    private ?AstNodeInterface $value;

    /**
     * @var \Schnell\Http\FQL\Ast\AstInterface|null
     */
    private ?AstInterface $parent;

    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var array|null
     */
    private ?array $childs;

    /**
     * @var \Doctrine\ORM\QueryBuilder|null
     */
    private ?QueryBuilder $queryBuilder;

    /**
     * @var \Schnell\Http\FQL\ParameterBag|null
     */
    private ?ParameterBag $parameterBag;

    /**
     * @psalm-api
     *
     * @param \Schnell\Http\FQL\Ast\Node\NodeInterface|null $value
     * @param \Schnell\Http\FQL\Ast\AstInterface|null $parent
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder
     * @param \Schnell\Http\FQL\ParameterBag|null $parameterBag
     * @param array|null $childs
     * @return static
     */
    public function __construct(
        ?AstNodeInterface $value = null,
        ?AstInterface $parent = null,
        ?QueryBuilder $queryBuilder = null,
        ?ParameterBag $parameterBag = null,
        ?array $childs = []
    ) {
        $this->setValue($value);
        $this->setParent($parent);
        $this->setQueryBuilder($queryBuilder);
        $this->setParameterBag($parameterBag);
        $this->setChilds($childs);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(): ?AstNodeInterface
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(?AstNodeInterface $value): void
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(): ?AstInterface
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function setParent(?AstInterface $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilder(): ?QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function setQueryBuilder(?QueryBuilder $queryBuilder): void
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterBag(): ?ParameterBag
    {
        return $this->parameterBag;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameterBag(?ParameterBag $parameterBag): void
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getChilds(): ?array
    {
        return $this->childs;
    }

    /**
     * {@inheritdoc}
     */
    public function setChilds(?array $childs): void
    {
        $this->childs = $childs;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(?AstInterface $child): void
    {
        $this->childs[] = $child;
    }

    /**
     * {@inheritdoc}
     */
    public function visit(?string $queryAlias)
    {
        if (null === $queryAlias || '' === $queryAlias) {
            return null;
        }

        /** @psalm-suppress NullReference */
        if (null === $this->getValue()->getInvokable($this)) {
            if (null === $this->getParameterBag()) {
                throw new FQLParserException("Parameter bag object is null");
            }

            if ($this->getValue()->getKey() === '') {
                throw new FQLParserException("Invalid key on DQL query condition.");
            }

            $parentType = $this->getParent()
                ->getValue()
                ->getType();
            $normalizedValue = $parentType === NodeTypes::EXPR_LIKEX
                ? '%' . $this->getValue()->getValue() . '%'
                : $this->getValue()->getValue();

            /** @psalm-suppress UndefinedInterfaceMethod */
            $this->getParameterBag()->setParameter(
                $this->getValue()->getKey(),
                $normalizedValue
            );

            /** @psalm-suppress UndefinedInterfaceMethod */
            return [
                $queryAlias . '.' . $this->getValue()->getKey(),
                ':' . $this->getValue()->getKey()
            ];
        }

        /**
         * @psalm-suppress PossiblyNullFunctionCall
         * @psalm-suppress PossiblyNullReference
         */
        return call_user_func_array(
            $this->getValue()->getInvokable($this),
            $this->getParameterList($queryAlias)
        );
    }

    /**
     * @psalm-api
     *
     * @param string|null $queryAlias
     * @return array
     */
    private function getParameterList(?string $queryAlias): array
    {
        $ret = [];

        /** @psalm-suppress PossiblyNullIterator */
        foreach ($this->getChilds() as $child) {
            $tmp = $child->visit($queryAlias);

            if (is_array($tmp)) {
                return $tmp;
            }

            $ret[] = $tmp;
        }

        return $ret;
    }
}
