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
class RepositoryStrategyInvocator implements RepositoryStrategyInvocatorInterface
{
    /**
     * @var \Schnell\Mapper\MapperInterface
     */
    private MapperInterface $mapper;

    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var \Lazis\Api\Repository\Strategy\RepositoryStrategyInterface
     */
    private RepositoryStrategyInterface $repositoryStrategy;

    /**
     * @param \Schnell\Mapper\MapperInterface $mapper
     * @param \Psr\Http\Message\RequestInterface $request
     */
    public function __construct(MapperInterface $mapper, RequestInterface $request)
    {
        $this->setMapper($mapper);
        $this->setRequest($request);
    }

    /**
     * {@inheritDoc}
     */
    public function getMapper(): MapperInterface
    {
        return $this->mapper;
    }

    /**
     * {@inheritDoc}
     */
    public function setMapper(MapperInterface $mapper): void
    {
        $this->mapper = $mapper;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * {@inheritDoc}
     */
    public function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * {@inheritDoc}
     */
    public function getRepositoryStrategy(): RepositoryStrategyInterface
    {
        return $this->repositoryStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function setRepositoryStrategy(RepositoryStrategyInterface $repositoryStrategy): void
    {
        $repositoryStrategy->setMapper($this->getMapper());
        $repositoryStrategy->setRequest($this->getRequest());

        $this->repositoryStrategy = $repositoryStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function invoke(SchemaInterface $schema): EntityInterface
    {
        return $this->repositoryStrategy->invoke($schema);
    }
}
