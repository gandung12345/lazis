<?php

declare(strict_types=1);

namespace Schnell\Middleware;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Schnell\Controller\ControllerPoolInterface;
use Schnell\Http\Code as HttpCode;
use Slim\Psr7\Response;

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ContentTypeMiddleware implements MiddlewareInterface
{
    use MiddlewareTrait;

    /**
     * @var \Schnell\Controller\ControllerPoolInterface
     */
    private ControllerPoolInterface $controllerPool;

    /**
     * @var array<string>
     */
    private array $allowedContentTypes = [
        'application/json',
        'application/hal+json',
        'multipart/form-data',
        'application/x-www-form-urlencoded',
        'application/octet-stream',
        'text/csv',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

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
        if (
            $request->getMethod() !== 'POST' &&
            $request->getMethod() !== 'PUT' &&
            $request->getMethod() !== 'PATCH'
        ) {
            return $this->injectCorsHeader($handler->handle($request));
        }

        if (false === $this->contains($request->getHeaderLine('Content-Type'))) {
            return $this->handleInvalidContentType();
        }

        if (
            null === $request->getParsedBody() &&
            ('application/json' === $request->getHeaderLine('Content-Type') ||
             'application/hal+json' === $request->getHeaderLine('Content-Type'))
        ) {
            return $this->handleEmptyBody();
        }

        return $this->injectCorsHeader($handler->handle($request));
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleInvalidContentType(): ResponseInterface
    {
        $response = new Response();
        $responseData = [
            'code' => HttpCode::BAD_REQUEST,
            'message' => sprintf(
                'Content type must be one of [%s]',
                join(', ', $this->allowedContentTypes)
            )
        ];

        /** @psalm-suppress PossiblyFalseArgument */
        $response->getBody()
            ->write(json_encode($responseData));

        $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(
                HttpCode::BAD_REQUEST,
                HttpCode::toString(HttpCode::BAD_REQUEST)
            );

        return $this->injectCorsHeader($response);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleEmptyBody(): ResponseInterface
    {
        $response = new Response();
        $responseData = [
            'code' => HttpCode::BAD_REQUEST,
            'message' => 'Got invalid json format or empty request body.'
        ];

        /** @psalm-suppress PossiblyFalseArgument */
        $response->getBody()
            ->write(json_encode($responseData));

        $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(
                HttpCode::BAD_REQUEST,
                HttpCode::toString(HttpCode::BAD_REQUEST)
            );

        return $this->injectCorsHeader($response);
    }

    /**
     * @param string $contentType
     * @return bool
     */
    private function contains(string $contentType): bool
    {
        foreach ($this->allowedContentTypes as $allowedContentType) {
            if (false !== stripos($contentType, $allowedContentType)) {
                return true;
            }
        }

        return false;
    }
}
