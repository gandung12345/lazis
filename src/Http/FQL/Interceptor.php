<?php

declare(strict_types=1);

namespace Schnell\Http\FQL;

use Doctrine\ORM\QueryBuilder;
use Schnell\Entity\EntityInterface;
use Schnell\Http\FQL\Ast\AstInterface;

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Interceptor implements InterceptorInterface
{
    /**
     * @var \Schnell\Http\FQL\Ast\AstInterface|null $ast
     */
    private ?AstInterface $ast;

    /**
     * @var \Doctrine\ORM\QueryBuilder|null $queryBuilder
     */
    private ?QueryBuilder $queryBuilder;

    /**
     * @var \Schnell\Entity\EntityInterface|null
     */
    private ?EntityInterface $entity;

    /**
     * @psalm-api
     *
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder
     * @param \Schnell\Http\FQL\Ast\AstInterface|null $ast
     * @param \Schnell\Entity\EntityInterface|null $entity
     * @return static
     */
    public function __construct(
        ?QueryBuilder $queryBuilder = null,
        ?AstInterface $ast = null,
        ?EntityInterface $entity = null,
    ) {
        $this->setAst($ast);
        $this->setQueryBuilder($queryBuilder);
        $this->setEntity($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function getAst(): ?AstInterface
    {
        return $this->ast;
    }

    /**
     * {@inheritdoc}
     */
    public function setAst(?AstInterface $ast): void
    {
        $this->ast = $ast;
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
     * {@inheritdoc}
     */
    public function intercept(): ?QueryBuilder
    {
        /** @psalm-suppress PossiblyNullReference */
        $queryAlias = $this->getEntity()->getQueryBuilderAlias();
        $queryBuilder = $this->getAst()->visit($queryAlias);

        if (null === $queryBuilder) {
            return null;
        }

        return $this->withInjectedQueryParameter($queryBuilder);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function withInjectedQueryParameter(QueryBuilder $queryBuilder): QueryBuilder
    {
        /**
         * @psalm-suppress PossiblyNullIterator
         * @psalm-suppress PossiblyNullReference
         */
        foreach ($this->getAst()->getParameterBag() as $key => $value) {
            $queryBuilder = $queryBuilder->setParameter($key, $value);
        }

        return $queryBuilder;
    }
}
