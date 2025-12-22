<?php

declare(strict_types=1);

namespace Schnell\Bridge\SlimCacheProvider;

use Schnell\Bridge\AbstractBridge;
use Slim\HttpCache\CacheProvider;

/**
 * @psalm-api
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class SlimCacheProviderBridge extends AbstractBridge
{
    /**
     * {@inheritdoc}
     */
    public function load(): void
    {
        /** @psalm-suppress PossiblyNullReference */
        $this->getContainer()->direct(CacheProvider::class, new CacheProvider());
        /** @psalm-suppress PossiblyNullReference */
        $this->getContainer()->alias(CacheProvider::class, $this->getAlias());
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'slim-cache-provider';
    }
}
