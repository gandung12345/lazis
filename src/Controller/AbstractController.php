<?php

declare(strict_types=1);

namespace Schnell\Controller;

use Schnell\ContainerInterface;
use Schnell\Config\ConfigInterface;
use Schnell\Hateoas\Hateoas;
use Schnell\Http\Code as HttpCode;
use Schnell\Paginator\PageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function class_exists;
use function sha1;

// help opcache.preload discover always-needed symbols
// phpcs:disable
class_exists(Hateoas::class);
// phpcs:enable

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
abstract class AbstractController implements ControllerInterface
{
    /**
     * @var \Schnell\ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var \Schnell\Config\ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @psalm-api
     *
     * @param \Schnell\ContainerInterface $container
     * @param \Schnell\Config\ConfigInterface $config
     * @return static
     */
    public function __construct(ContainerInterface $container, ConfigInterface $config)
    {
        $this->setContainer($container);
        $this->setConfig($config);
    }

    /**
     * {@inheritDoc}
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
    public function setConfig(ConfigInterface $config): void
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function withEtag(ResponseInterface $response): ResponseInterface
    {
        return $this->getContainer()
            ->get('slim-cache-provider')
            ->withEtag($response, sha1($response->getBody()->getContents()));
    }

    /**
     * {@inheritDoc}
     */
    public function json(
        ResponseInterface $response,
        array $data,
        int $code = HttpCode::OK
    ): ResponseInterface {
        /** @psalm-suppress PossiblyNullArgument */
        $clonedResponse = $response
            ->withStatus($code, HttpCode::toString($code))
            ->withHeader('Content-Type', 'application/json');

        /** @psalm-suppress PossiblyFalseArgument */
        $clonedResponse->getBody()->write(json_encode($data));

        return $this->withEtag($clonedResponse);
    }

    /**
     * {@inheritDoc}
     */
    public function hateoas(
        RequestInterface $request,
        ResponseInterface $response,
        PageInterface $page,
        array $data,
        int $code = HttpCode::OK
    ): ResponseInterface {
        $data = (new Hateoas($data, $page, $request))
            ->generate();

        /** @psalm-suppress PossiblyNullArgument */
        $clonedResponse = $response
            ->withStatus($code, HttpCode::toString($code))
            ->withHeader('Content-Type', 'application/hal+json');

        /** @psalm-suppress PossiblyFalseArgument */
        $clonedResponse->getBody()->write(json_encode($data));

        return $this->withEtag($clonedResponse);
    }
}
