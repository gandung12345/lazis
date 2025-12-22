<?php

declare(strict_types=1);

namespace Schnell\Bridge\Cache\Factory;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class FilesystemCacheFactory extends AbstractCacheFactory
{
    /**
     * {@inheritDoc}
     */
    public function createCache(): CacheItemPoolInterface
    {
        return new FilesystemAdapter(
            $this->getConfig()->get('cache.namespace'),
            $this->getConfig()->get('cache.defaultLifetime'),
            $this->getConfig()->get('cache.directory')
        );
    }
}
