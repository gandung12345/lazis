<?php

declare(strict_types=1);

namespace Schnell\Bridge\Cache;

use Schnell\Config\ConfigInterface;
use Schnell\Bridge\Cache\Exception\CacheFactoryException;
use Schnell\Bridge\Cache\Factory\ApcuCacheFactory;
use Schnell\Bridge\Cache\Factory\ArrayCacheFactory;
use Schnell\Bridge\Cache\Factory\FilesystemCacheFactory;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class CacheFlyweightFactory implements CacheFlyweightFactoryInterface
{
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
    public function createFactory(string $cacheDriver): CacheFactoryInterface
    {
        return match ($cacheDriver) {
            'apcu' => new ApcuCacheFactory($this->getConfig()),
            'array' => new ArrayCacheFactory($this->getConfig()),
            'file' => new FilesystemCacheFactory($this->getConfig()),
            default => throw new CacheFactoryException(
                sprintf('Cache factory with identifier \'%s\' not found', $cacheDriver)
            )
        };
    }

    /**
     * @return \Schnell\Config\ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * @param \Schnell\Config\ConfigInterface $config
     * @return void
     */
    public function setConfig(ConfigInterface $config): void
    {
        $this->config = $config;
    }
}
