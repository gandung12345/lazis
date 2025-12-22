<?php

declare(strict_types=1);

namespace Schnell\Http\FQL;

use Doctrine\ORM\QueryBuilder;
use Psr\Http\Message\RequestInterface;
use Schnell\Entity\EntityInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface InterceptorFactoryInterface
{
    /**
     * @return \Psr\Http\Message\RequestInterface|null
     */
    public function getRequest(): ?RequestInterface;

    /**
     * @param \Psr\Http\Message\RequestInterface|null $request
     * @return void
     */
    public function setRequest(?RequestInterface $request): void;

    /**
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
     * @param \Schnell\Entity\EntityInterface $entity
     * @return void
     */
    public function setEntity(?EntityInterface $entity): void;

    /**
     * @psalm-api
     *
     * @return \Schnell\Http\FQL\InterceptorInterface|null
     */
    public function createInterceptor(): ?InterceptorInterface;
}
