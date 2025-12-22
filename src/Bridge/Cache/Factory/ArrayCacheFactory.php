<?php

declare(strict_types=1);

namespace Schnell\Bridge\Cache\Factory;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ArrayCacheFactory extends AbstractCacheFactory
{
    /**
     * {@inheritDoc}
     */
    public function createCache(): CacheItemPoolInterface
    {
        return new ArrayAdapter(
            $this->getConfig()->get('cache.defaultLifetime'),
            true,
            $this->getConfig()->get('cache.maxStorageLifetime'),
            $this->getConfig()->get('cache.maxItems')
        );
    }
}
