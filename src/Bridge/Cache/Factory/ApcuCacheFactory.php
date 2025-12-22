<?php

declare(strict_types=1);

namespace Schnell\Bridge\Cache\Factory;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ApcuAdapter;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ApcuCacheFactory extends AbstractCacheFactory
{
    /**
     * {@inheritDoc}
     */
    public function createCache(): CacheItemPoolInterface
    {
        $this->checkExtension('apcu');

        return new ApcuAdapter(
            $this->getConfig()->get('cache.namespace'),
            $this->getConfig()->get('cache.defaultLifetime')
        );
    }
}
