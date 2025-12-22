<?php

declare(strict_types=1);

namespace Schnell\Mapper;

use Psr\Http\Message\RequestInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface RequestAwareInterface
{
    /**
     * @psalm-api
     *
     * @return \Psr\Http\Message\RequestInterface|null
     */
    public function getRequest(): ?RequestInterface;

    /**
     * @param \Psr\Http\Message\RequestInterface|null $request
     * @return void
     */
    public function setRequest(?RequestInterface $request): void;

    /**
     * @psalm-api
     *
     * @param \Psr\Http\Message\RequestInterface|null $request
     * @return \Schnell\Mapper\MapperInterface
     */
    public function withRequest(?RequestInterface $request): MapperInterface;
}
