<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class OffBalanceSheetAggregation
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int MOSQUE = 1;

    /**
     * @var int
     */
    public const int UPZIS = 2;

    /**
     * @var int
     */
    public const int JPZIS = 3;
}
