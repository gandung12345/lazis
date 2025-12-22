<?php

declare(strict_types=1);

namespace Schnell\Bridge\Cache\Factory;

use Psr\Cache\CacheItemPoolInterface;
use Schnell\Bridge\Cache\CacheFactoryInterface;
use Schnell\Config\ConfigInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
abstract class AbstractCacheFactory implements CacheFactoryInterface
{
    use CacheExtensionTrait;

    /**
     * @var \Schnell\Config\ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @param \Schnell\Config\ConfigInterface $config
     * @return static
     */
    public function __construct(ConfigInterface $config)
    {
        $this->setConfig($config);
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
    abstract public function createCache(): CacheItemPoolInterface;
}
