<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class NonHalalFunding
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int BANK_INTEREST = 1;

    /**
     * @var int
     */
    public const int CURRENT_ACCOUNT_SERVICE = 2;

    /**
     * @var int
     */
    public const int OTHER = 3;
}
