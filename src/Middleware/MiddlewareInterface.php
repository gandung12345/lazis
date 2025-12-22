<?php

declare(strict_types=1);

namespace Schnell\Middleware;

use Schnell\Controller\ControllerPoolInterface;
use Psr\Http\Server\MiddlewareInterface as PsrMiddlewareInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface MiddlewareInterface extends PsrMiddlewareInterface
{
    /**
     * @psalm-api
     *
     * @return \Schnell\Controller\ControllerPoolInterface
     */
    public function getControllerPool(): ControllerPoolInterface;

    /**
     * @param \Schnell\Controller\ControllerPoolInterface $controllerPool
     * @return void
     */
    public function setControllerPool(
        ControllerPoolInterface $controllerPool
    ): void;
}
