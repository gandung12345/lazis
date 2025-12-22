<?php

declare(strict_types=1);

namespace Schnell\Bridge;

use Schnell\ContainerInterface;
use Schnell\Config\ConfigInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
abstract class AbstractBridge implements BridgeInterface
{
    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var \Schnell\Config\ConfigInterface|null
     */
    private ?ConfigInterface $config;

    /**
     * @var \Schnell\ContainerInterface|null
     */
    private ?ContainerInterface $container;

    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var string
     */
    private ?string $basePath;

    /**
     * @param \Schnell\Config\ConfigInterface|null $config
     * @param \Schnell\ContainerInterface|null $container
     * @param string|null $basePath
     * @return static
     *
     * @psalm-api
     * @psalm-param \Schnell\Config\ConfigInterface|null $config
     * @psalm-param \Schnell\ContainerInterface|null $container
     * @psalm-ignore-nullable-return
     */
    public function __construct(
        ?ConfigInterface $config = null,
        ?ContainerInterface $container = null,
        ?string $basePath = null,
    ) {
        $this->config = $config;
        $this->container = $container;
        $this->basePath = $basePath;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig(): ConfigInterface|null
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
    public function setConfig(ConfigInterface|null $config): void
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getContainer(): ContainerInterface|null
    {
        return $this->container;
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface|null $container): void
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getBasePath(): string|null
    {
        return $this->basePath;
    }

    /**
     * {@inheritDoc}
     */
    public function setBasePath(string|null $path): void
    {
        $this->basePath = $path;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function load(): void;

    /**
     * {@inheritDoc}
     */
    abstract public function getAlias(): string;
}
