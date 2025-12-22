<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class NonHalalDistribution
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int BANK_ADMIN = 1;

    /**
     * @var int
     */
    public const int NON_HALAL_FUNDING_USAGE = 2;
}
