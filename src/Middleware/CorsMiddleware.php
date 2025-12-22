<?php

declare(strict_types=1);

namespace Schnell\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Schnell\Controller\ControllerPoolInterface;
use Slim\Psr7\Response;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class CorsMiddleware implements MiddlewareInterface
{
    use MiddlewareTrait;

    /**
     * @var \Schnell\Controller\ControllerPoolInterface
     */
    private ControllerPoolInterface $controllerPool;

    /**
     * @psalm-api
     *
     * @param \Schnell\Controller\ControllerPoolInterface $controllerPool
     * @return static
     */
    public function __construct(ControllerPoolInterface $controllerPool)
    {
        $this->setControllerPool($controllerPool);
    }

    /**
     * {@inheritDoc}
     */
    public function getControllerPool(): ControllerPoolInterface
    {
        return $this->controllerPool;
    }

    /**
     * {@inheritDoc}
     */
    public function setControllerPool(ControllerPoolInterface $controllerPool): void
    {
        $this->controllerPool = $controllerPool;
    }

    /**
     * {@inheritDoc}
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $request->getMethod() === 'OPTIONS'
            ? new Response()
            : $handler->handle($request);

        return $this->injectCorsHeader($response);
    }
}
