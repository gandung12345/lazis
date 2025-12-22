<?php

declare(strict_types=1);

namespace Schnell\Middleware;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Schnell\Controller\ControllerPoolInterface;
use Schnell\Exception\MapperException;
use Schnell\Exception\ValidatorException;
use Schnell\Http\Code as HttpCode;
use Slim\Exception\HttpSpecializedException;
use Slim\Psr7\Response;

use function class_exists;
use function json_encode;

// help opcache.preload discover always-needed symbols
// phpcs:disable
class_exists(Throwable::class);
class_exists(HttpNotFoundException::class);
class_exists(Response::class);
// phpcs:enable

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class HttpErrorMiddleware implements MiddlewareInterface
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
     * {@inheritdoc}
     */
    public function getControllerPool(): ControllerPoolInterface
    {
        return $this->controllerPool;
    }

    /**
     * {@inheritdoc}
     */
    public function setControllerPool(
        ControllerPoolInterface $controllerPool
    ): void {
        $this->controllerPool = $controllerPool;
    }

    /**
     * {@inheritdoc}
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (HttpSpecializedException $e) {
            return $this->handleHttpSpecializedException($e);
        } catch (MapperException $e) {
            return $this->handleMapperException($e);
        } catch (ValidatorException $e) {
            return $this->handleGenericException($e, HttpCode::BAD_REQUEST);
        } catch (Throwable $e) {
            dd($e);
            return $this->handleGenericException($e);
        }
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress UnusedParam
     *
     * @param \Throwable $e
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleHttpSpecializedException(Throwable $e): ResponseInterface
    {
        $response = new Response();

        /** @psalm-suppress UndefinedInterfaceMethod */
        $responseData = [
            'code' => $e->getCode(),
            'path' => $e->getRequest()->getUri()->getPath(),
            'message' => $e->getMessage()
        ];

        /** @psalm-suppress PossiblyFalseArgument */
        $response->getBody()
            ->write(json_encode($responseData));

        $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($e->getCode(), HttpCode::toString($e->getCode()));

        return $this->injectCorsHeader($response);
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress PossiblyUnusedParam
     *
     * @param \Throwable $e
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleMapperException(Throwable $e): ResponseInterface
    {
        $response = new Response();

        /** @psalm-suppress UndefinedInterfaceMethod */
        $responseData = [
            'code' => HttpCode::INTERNAL_SERVER_ERROR,
            'sqlState' => $e->getSqlState(),
            'sqlStateDescription' => $e->getSqlStateDescription(),
            'message' => $e->getMessage()
        ];

        /** @psalm-suppress PossiblyFalseArgument */
        $response->getBody()
            ->write(json_encode($responseData));

        $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(
                HttpCode::INTERNAL_SERVER_ERROR,
                HttpCode::toString(HttpCode::INTERNAL_SERVER_ERROR)
            );

        return $this->injectCorsHeader($response);
    }

    /**
     * @param \Throwable $e
     * @param int $httpCode
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleGenericException(
        Throwable $e,
        int $httpCode = HttpCode::INTERNAL_SERVER_ERROR
    ): ResponseInterface {
        $response = new Response();
        $responseData = [
            'code' => $httpCode,
            'message' => $e->getMessage()
        ];

        /** @psalm-suppress PossiblyFalseArgument */
        $response->getBody()
            ->write(json_encode($responseData));

        $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($httpCode, HttpCode::toString($httpCode));

        return $this->injectCorsHeader($response);
    }
}
