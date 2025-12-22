<?php

declare(strict_types=1);

namespace Schnell\Bridge\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Schnell\Config\ConfigInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface CacheFactoryInterface
{
    /**
     * @return \Psr\Cache\CacheItemPoolInterface
     */
    public function createCache(): CacheItemPoolInterface;

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
