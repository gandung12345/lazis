<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class NuCoinCrossTransfer
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int QUEUED = 1;

    /**
     * @var int
     */
    public const int APPROVED = 2;

    /**
     * @var int
     */
    public const int REJECTED = 3;
}
