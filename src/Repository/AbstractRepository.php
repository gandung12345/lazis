<?php

declare(strict_types=1);

namespace Schnell\Repository;

use Psr\Http\Message\RequestInterface;
use Schnell\Mapper\MapperInterface;

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
abstract class AbstractRepository implements RepositoryInterface
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
     * @psalm-api
     *
     * @param \Schnell\Mapper\MapperInterface $mapper
     * @param \Psr\Http\Message\RequestInterface $request
     * @return static
     */
    public function __construct(MapperInterface $mapper, RequestInterface $request)
    {
        $this->setMapper($mapper);
        $this->setRequest($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getMapper(): MapperInterface
    {
        return $this->mapper;
    }

    /**
     * {@inheritdoc}
     */
    public function setMapper(MapperInterface $mapper): void
    {
        $this->mapper = $mapper;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }
}
