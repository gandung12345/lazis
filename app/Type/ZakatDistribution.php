<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class ZakatDistribution
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int NU_CARE_SMART = 1;

    /**
     * @var int
     */
    public const int NU_CARE_EMPOWERED = 2;

    /**
     * @var int
     */
    public const int NU_CARE_HEALTHY = 3;

    /**
     * @var int
     */
    public const int NU_CARE_GREEN = 4;

    /**
     * @var int
     */
    public const int NU_CARE_PEACE = 5;

    /**
     * @var int
     */
    public const int NU_CARE_QURBAN = 6;
}
