<?php

declare(strict_types=1);

namespace Schnell\Bridge\Cache;

use Schnell\Bridge\AbstractBridge;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class CacheBridge extends AbstractBridge
{
    /**
     * {@inheritDoc}
     */
    public function load(): void
    {
        $flyweight = new CacheFlyweightFactory($this->getConfig());
        $factory = $flyweight->createFactory($this->getConfig()->get('cache.driver'));

        $this->getContainer()->direct(
            CacheItemPoolInterface::class,
            $factory->createCache()
        );

        $this->getContainer()->alias(CacheItemPoolInterface::class, $this->getAlias());
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias(): string
    {
        return 'cache';
    }
}
