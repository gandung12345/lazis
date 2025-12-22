<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class AmilFunding
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int GRANT_FUNDS = 1;

    /**
     * @var int
     */
    public const int OTHER_AMIL = 2;
}
