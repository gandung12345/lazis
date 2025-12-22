<?php

declare(strict_types=1);

namespace Lazis\Api\Middleware;

use DateTimeImmutable;
use ReflectionClass;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Schnell\Attribute\Auth\Auth;
use Schnell\Controller\ControllerInterface;
use Schnell\Controller\ControllerPoolInterface;
use Schnell\Http\Code as HttpCode;
use Schnell\Middleware\MiddlewareInterface;
use Schnell\Middleware\MiddlewareTrait;
use Slim\Routing\Route;
use Slim\Routing\RouteContext;
use Slim\Psr7\Response;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class RoleMiddleware implements MiddlewareInterface
{
    use MiddlewareTrait;

    /**
     * @var array
     */
    private $excluded = [
        '/token',
        '/organizer/{id}',
        '/volunteer/{id}'
    ];

    /**
     * @var \Schnell\Controller\ControllerPoolInterface
     */
    private ControllerPoolInterface $controllerPool;

    /**
     * @psalm-api
     *
     * @param \Schnell\Controller\ControllerInterface $controllerPool
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
     * {@inheritDoc}
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        if (!$route || !$route->getCallable()) {
            return $this->handleInvalidRouteMetadata();
        }

        if ($this->isExcluded($request, $route)) {
            return $this->injectCorsHeader($handler->handle($request));
        }

        $callable = $route->getCallable();

        if (!$request->hasHeader('X-Authorization')) {
            return $this->handleNonexistentAuthorizationHeader();
        }

        $tokenBody = $this->deserializeTokenBody($request);

        if ($tokenBody instanceof ResponseInterface) {
            return $tokenBody;
        }

        $authorized = $this->isAuthorized(
            $tokenBody['userScope'],
            $callable[0],
            $callable[1]
        );

        if (false === $authorized) {
            return $this->handleForbidden();
        }

        return $this->injectCorsHeader($handler->handle($request));
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleInvalidRouteMetadata(): ResponseInterface
    {
        $response = new Response();
        $responseData = [
            'code' => HttpCode::BAD_REQUEST,
            'message' => 'Route metadata is invalid or null.'
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
    private function handleNonexistentAuthorizationHeader(): ResponseInterface
    {
        $response = new Response();
        $responseData = [
            'code' => HttpCode::UNAUTHORIZED,
            'message' => '\'X-Authorization\' header is not present.'
        ];

        /** @psalm-suppress PossiblyFalseArgument */
        $response->getBody()
            ->write(json_encode($responseData));

        $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(
                HttpCode::UNAUTHORIZED,
                HttpCode::toString(HttpCode::UNAUTHORIZED)
            );

        return $this->injectCorsHeader($response);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleForbidden(): ResponseInterface
    {
        $response = new Response();
        $responseData = [
            'code' => HttpCode::FORBIDDEN,
            'message' => 'Forbidden access to this route. Insufficient role.'
        ];

        /** @psalm-suppress PossiblyFalseArgument */
        $response->getBody()
            ->write(json_encode($responseData));

        $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(
                HttpCode::FORBIDDEN,
                HttpCode::toString(HttpCode::FORBIDDEN)
            );

        return $this->injectCorsHeader($response);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleInvalidOrExpiredToken(): ResponseInterface
    {
        $response = new Response();
        $responseData = [
            'code' => HttpCode::UNAUTHORIZED,
            'message' => 'Invalid or expired or invalid signature ' .
                         'JWT bearer token'
        ];

        /** @psalm-suppress PossiblyFalseArgument */
        $response->getBody()
            ->write(json_encode($responseData));

        $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(
                HttpCode::UNAUTHORIZED,
                HttpCode::toString(HttpCode::UNAUTHORIZED)
            );

        return $this->injectCorsHeader($response);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Slim\Routing\Route
     * @return bool
     */
    private function isExcluded(
        ServerRequestInterface $request,
        Route $route
    ): bool {
        return in_array($route->getPattern(), $this->excluded, true);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|array
     */
    private function deserializeTokenBody(
        ServerRequestInterface $request
    ): ResponseInterface|array {
        $config = $this->getControllerPool()->getConfig();
        $token = $request->getHeaderLine('X-Authorization');
        $secret = InMemory::plainText($config->get('app.secret'));
        $now = new DateTimeImmutable();

        try {
            $parsed = (new JwtFacade())->parse(
                $token,
                new Constraint\SignedWith(new Sha256(), $secret),
                new Constraint\StrictValidAt(
                    new FrozenClock($now)
                )
            );
        } catch (RequiredConstraintsViolated | InvalidTokenStructure $e) {
            $parsed = null;
        }

        if (null === $parsed) {
            return $this->handleInvalidOrExpiredToken();
        }

        return $parsed->claims()->get('data');
    }

    /**
     * @param int $authRole
     * @param \Schnell\Controller\ControllerInterface $controller
     * @param string $method
     * @return bool
     */
    private function isAuthorized(
        int $authRole,
        ControllerInterface $controller,
        string $method
    ): bool {
        $reflection = new ReflectionClass($controller);
        $targetMethod = $reflection->getMethod($method);
        $attributes = $targetMethod->getAttributes(Auth::class);

        if (sizeof($attributes) === 0) {
            return false;
        }

        return in_array(
            $authRole,
            $attributes[0]->newInstance()->getRole(),
            true
        );
    }
}
