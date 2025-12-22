<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class NuCoinCrossTransactionIncomingStrategy
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int BRANCH_REPRESENTATIVE_FROM_BRANCH = 1;

    /**
     * @var int
     */
    public const int TWIG_FROM_BRANCH_REPRESENTATIVE = 2;
}
