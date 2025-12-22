<?php

declare(strict_types=1);

namespace Schnell\Bridge\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Schnell\Config\ConfigInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface CacheFlyweightFactoryInterface
{
    /**
     * @param string $cacheDriver
     * @return \Schnell\Bridge\Cache\CacheFactoryInterface
     */
    public function createFactory(string $cacheDriver): CacheFactoryInterface;

    /**
     * @return \Schnell\Config\ConfigInterface
     */
    public function getConfig(): ConfigInterface;

    /**
     * @param \Schnell\Config\ConfigInterface $config
     * @return void
     */
    public function setConfig(ConfigInterface $config): void;
}
