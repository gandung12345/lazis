<?php

declare(strict_types=1);

namespace Schnell\Http\FQL;

use Doctrine\ORM\QueryBuilder;
use Schnell\Entity\EntityInterface;
use Schnell\Http\FQL\Ast\AstInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface InterceptorInterface
{
    /**
     * @return \Schnell\Http\FQL\Ast\AstInterface|null
     */
    public function getAst(): ?AstInterface;

    /**
     * @param \Schnell\Http\FQL\Ast\AstInterface|null $ast
     * @return void
     */
    public function setAst(?AstInterface $ast): void;

    /**
     * @psalm-api
     *
     * @return \Doctrine\ORM\QueryBuilder|null
     */
    public function getQueryBuilder(): ?QueryBuilder;

    /**
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder
     * @return void
     */
    public function setQueryBuilder(?QueryBuilder $queryBuilder): void;

    /**
     * @return \Schnell\Entity\EntityInterface|null
     */
    public function getEntity(): ?EntityInterface;

    /**
     * @param \Schnell\Entity\EntityInterface|null $entity
     * @return void
     */
    public function setEntity(?EntityInterface $entity): void;

    /**
     * @psalm-api
     *
     * @return \Doctrine\ORM\QueryBuilder|null
     */
    public function intercept(): ?QueryBuilder;
}
