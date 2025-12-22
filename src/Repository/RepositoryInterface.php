<?php

declare(strict_types=1);

namespace Schnell\Repository;

use Psr\Http\Message\RequestInterface;
use Schnell\Mapper\MapperInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface RepositoryInterface
{
    /**
     * @psalm-api
     *
     * @return \Schnell\Mapper\MapperInterface
     */
    public function getMapper(): MapperInterface;

    /**
     * @param \Schnell\Mapper\MapperInterface $mapper
     * @return void
     */
    public function setMapper(MapperInterface $mapper): void;

    /**
     * @psalm-api
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @return void
     */
    public function setRequest(RequestInterface $request): void;
}
