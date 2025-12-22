<?php

declare(strict_types=1);

namespace Schnell;

use Psr\Http\Message\RequestInterface;
use Schnell\ContainerInterface;
use Schnell\Bridge\BridgeInterface;
use Schnell\Config\ConfigInterface;
use Schnell\Controller\ControllerResolverInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface KernelInterface
{
    /**
     * @return \Schnell\Config\ConfigInterface
     */
    public function getConfig(): ConfigInterface;

    /**
     * @param \Schnell\Config\ConfigInterface $config
     * @return void
     */
    public function setConfig(ConfigInterface $config): void;

    /**
     * @return \Schnell\ContainerInterface
     */
    public function getContainer(): ContainerInterface;

    /**
     * @param \Schnell\ContainerInterface $container
     * @return void
     */
    public function setContainer(ContainerInterface $container): void;

    /**
     * @return \Schnell\Controller\ControllerResolverInterface
     */
    public function getControllerResolver(): ControllerResolverInterface;

    /**
     * @param \Schnell\Controller\ControllerResolverInterface $controllerResolver
     * @return void
     */
    public function setControllerResolver(
        ControllerResolverInterface $controllerResolver
    ): void;

    /**
     * @psalm-api
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @return void
     */
    public function handle(RequestInterface $request): void;

    /**
     * @psalm-api
     *
     * @param \Schnell\Bridge\BridgeInterface $extension
     * @param string|null $basePath
     * @return \Schnell\KernelInterface
     */
    public function addExtension(
        BridgeInterface $extension,
        string|null $basePath = null
    ): KernelInterface;

    /**
     * @psalm-api
     *
     * @return void
     */
    public function load(): void;
}
