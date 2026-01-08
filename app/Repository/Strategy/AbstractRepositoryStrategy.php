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
abstract class AbstractRepositoryStrategy implements RepositoryStrategyInterface
{
    /**
     * @var \Schnell\Mapper\MapperInterface|null
     */
    private ?MapperInterface $mapper;

    /**
     * @var \Psr\Http\Message\RequestInterface|null
     */
    private ?RequestInterface $request;

    /**
     * @param \Schnell\Mapper\MapperInterface|null $mapper
     * @param \Psr\Http\Message\RequestInterface|null $request
     */
    public function __construct(?MapperInterface $mapper = null, ?RequestInterface $request = null)
    {
        $this->setMapper($mapper);
        $this->setRequest($request);
    }

    /**
     * {@inheritDoc}
     */
    public function getMapper(): ?MapperInterface
    {
        return $this->mapper;
    }

    /**
     * {@inheritDoc}
     */
    public function setMapper(?MapperInterface $mapper): void
    {
        $this->mapper = $mapper;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    /**
     * {@inheritDoc}
     */
    public function setRequest(?RequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function invoke(SchemaInterface $schema): EntityInterface;

    /**
     * {@inheritDoc}
     */
    abstract public function repositoryStrategyId(): string;
}
