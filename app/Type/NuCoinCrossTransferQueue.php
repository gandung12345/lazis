<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

final class NuCoinCrossTransferQueue
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int DEPOSIT = 1;

    /**
     * @var int
     */
    public const int REFUND = 2;
}
