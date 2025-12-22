<?php

declare(strict_types=1);

namespace Lazis\Api\Repository\Strategy;

use Psr\Http\Message\RequestInterface;
use Schnell\Mapper\MapperInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface ConcreteRepositoryStrategyAwareInterface
{
    /**
     * @return \Schnell\Mapper\MapperInterface|null
     */
    public function getMapper(): ?MapperInterface;

    /**
     * @param \Schnell\Mapper\MapperInterface|null $mapper
     * @return void
     */
    public function setMapper(?MapperInterface $mapper): void;

    /**
     * @return \Psr\Http\Message\RequestInterface|null
     */
    public function getRequest(): ?RequestInterface;

    /**
     * @param \Psr\Http\Message\RequestInterface|null $request
     * @return void
     */
    public function setRequest(?RequestInterface $request): void;
}
