<?php

declare(strict_types=1);

namespace Schnell\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

use function class_exists;

// help opcache.preload discover always-needed symbols
// @codeCoverageIgnoreStart
// phpcs:disable
class_exists(Exception::class);
// phpcs:enable
// @codeCoverageIgnoreEnd

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NotFoundException extends Exception implements NotFoundExceptionInterface
{
}
