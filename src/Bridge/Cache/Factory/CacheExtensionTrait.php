<?php

declare(strict_types=1);

namespace Schnell\Bridge\Cache\Factory;

use Schnell\Bridge\Cache\Exception\CacheExtensionException;

use function extension_loaded;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
trait CacheExtensionTrait
{
    /**
     * @param string $extension
     * @throws \Schnell\Bridge\Cache\Extension\CacheExtensionException
     */
    protected function checkExtension(string $extension): void
    {
        if (!extension_loaded($extension)) {
            throw new CacheExtensionException(
                sprintf('Extension \'%s\' not loaded.', $extension)
            );
        }
    }
}
