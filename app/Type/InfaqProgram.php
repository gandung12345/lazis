<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class InfaqProgram
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
    public const int CAMPAIGN_PROGRAM = 6;

    /**
     * @var int
     */
    public const int DONATION = 7;

    /**
     * @var int
     */
    public const int UNBOUNDED = 8;
}
