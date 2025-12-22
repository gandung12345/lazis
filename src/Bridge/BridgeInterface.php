<?php

declare(strict_types=1);

namespace Schnell\Bridge;

use Schnell\ContainerInterface;
use Schnell\Config\ConfigInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface BridgeInterface
{
    /**
     * @return \Schnell\Config\ConfigInterface|null
     */
    public function getConfig(): ConfigInterface|null;

    /**
     * @param \Schnell\Config\ConfigInterface|null $config
     * @return void
     */
    public function setConfig(ConfigInterface|null $config): void;

    /**
     * @return \Schnell\ContainerInterface|null
     */
    public function getContainer(): ContainerInterface|null;

    /**
     * @param \Schnell\ContainerInterface|null $container
     * @return void
     */
    public function setContainer(ContainerInterface|null $container): void;

    /**
     * @return string|null
     */
    public function getBasePath(): string|null;

    /**
     * @param string|null $path
     * @return void
     */
    public function setBasePath(string|null $path): void;

    /**
     * @psalm-api
     *
     * @return void
     */
    public function load(): void;

    /**
     * @return string
     */
    public function getAlias(): string;
}
