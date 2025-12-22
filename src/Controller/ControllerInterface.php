<?php

declare(strict_types=1);

namespace Schnell\Controller;

use Schnell\ContainerInterface;
use Schnell\Config\ConfigInterface;
use Schnell\Http\Code as HttpCode;
use Schnell\Paginator\PageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface ControllerInterface
{
    /**
     * @psalm-api
     *
     * @return \Schnell\ContainerInterface
     */
    public function getContainer(): ContainerInterface;

    /**
     * @param \Schnell\ContainerInterface $container
     * @return void
     */
    public function setContainer(ContainerInterface $container): void;

    /**
     * @psalm-api
     *
     * @return \Schnell\Config\ConfigInterface
     */
    public function getConfig(): ConfigInterface;

    /**
     * @param \Schnell\Config\ConfigInterface $config
     * @return void
     */
    public function setConfig(ConfigInterface $config): void;

    /**
     * @psalm-api
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function withEtag(ResponseInterface $response): ResponseInterface;

    /**
     * @psalm-api
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $data
     * @param int $code
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function json(
        ResponseInterface $response,
        array $data,
        int $code = HttpCode::OK
    ): ResponseInterface;

    /**
     * @psalm-api
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Schnell\Paginator\PageInterface $page
     * @param array $data
     * @param int $code
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function hateoas(
        RequestInterface $request,
        ResponseInterface $response,
        PageInterface $page,
        array $data,
        int $code = HttpCode::OK
    ): ResponseInterface;
}
