<?php

declare(strict_types=1);

namespace Schnell\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Middleware\BodyParsingMiddleware;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\RoutingMiddleware;

use function class_exists;

// help opcache.preload discover always-needed symbols
// phpcs:disable
class_exists(BodyParsingMiddleware::class);
class_exists(ErrorMiddleware::class);
class_exists(RoutingMiddleware::class);
// phpcs:enable

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface ControllerResolverInterface extends RequestHandlerInterface
{
    /**
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

    /**
     * @return \Slim\Interfaces\RouteCollectorProxyInterface
     */
    public function getRouteCollectorProxy(): RouteCollectorProxyInterface;

    /**
     * @param \Slim\Interfaces\RouteCollectorProxyInterface $routeCollectorProxy
     * @return void
     */
    public function setRouteCollectorProxy(
        RouteCollectorProxyInterface $routeCollectorProxy
    ): void;

    /**
     * @psalm-api
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return void
     */
    public function resolve(ServerRequestInterface $request): void;

    /**
     * @psalm-api
     *
     * @param \Psr\Http\Message\ServerRequestInterface|null $request
     * @return void
     */
    public function run(?ServerRequestInterface $request = null): void;

    /**
     * @param \Psr\Http\Server\MiddlewareInterface $middleware
     * @return void
     */
    public function add(MiddlewareInterface $middleware): void;

    /**
     * @psalm-api
     *
     * @return \Slim\Middleware\RoutingMiddleware
     */
    public function addRoutingMiddleware(): RoutingMiddleware;

    /**
     * @psalm-api
     *
     * @param bool $displayErrorDetails
     * @param bool $logErrors
     * @param bool $logErrorDetails
     * @param \Psr\Log\LoggerInterface|null $logger
     * @return \Slim\Middleware\ErrorMiddleware
     */
    public function addErrorMiddleware(
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails,
        ?LoggerInterface $logger = null
    ): ErrorMiddleware;

    /**
     * @psalm-api
     *
     * @param array $bodyParsers
     * @return \Slim\Middleware\BodyParsingMiddleware
     */
    public function addBodyParsingMiddleware(
        array $bodyParsers = []
    ): BodyParsingMiddleware;

    /**
     * @psalm-api
     *
     * @return void
     */
    public function addHttpCache(): void;
}
