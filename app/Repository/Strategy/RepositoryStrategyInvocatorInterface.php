<?php

declare(strict_types=1);

namespace Lazis\Api\Repository\Strategy;

use Psr\Http\Message\RequestInterface;
use Schnell\Entity\EntityInterface;
use Schnell\Mapper\MapperInterface;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface RepositoryStrategyInvocatorInterface
{
    /**
     * @return \Schnell\Mapper\MapperInterface
     */
    public function getMapper(): MapperInterface;

    /**
     * @param \Schnell\Mapper\MapperInterface $mapper
     * @return void
     */
    public function setMapper(MapperInterface $mapper): void;

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @return void
     */
    public function setRequest(RequestInterface $request): void;

    /**
     * @return \Lazis\Api\Repository\Strategy\RepositoryStrategyInterface
     */
    public function getRepositoryStrategy(): RepositoryStrategyInterface;

    /**
     * @param \Lazis\Api\Repository\Strategy\RepositoryStrategyInterface $repositoryStrategy
     * @return void
     */
    public function setRepositoryStrategy(RepositoryStrategyInterface $repositoryStrategy): void;

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface
     */
    public function invoke(SchemaInterface $schema): EntityInterface;
}
