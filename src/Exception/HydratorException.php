<?php

declare(strict_types=1);

namespace Schnell\Exception;

use Exception;

use function class_exists;

// help opcache.preload discover always-needed symbols
// phpcs:disable
class_exists(Exception::class);
// phpcs:enable

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class HydratorException extends Exception
{
}
