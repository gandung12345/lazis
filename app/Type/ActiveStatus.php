<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class ActiveStatus
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int ACTIVE = 1;

    /**
     * @var int
     */
    public const int INACTIVE = 2;
}
