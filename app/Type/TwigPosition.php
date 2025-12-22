<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class TwigPosition
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int RAIS = 28;

    /**
     * @var int
     */
    public const int LEADER = 29;

    /**
     * @var int
     */
    public const int SUB_AREA_MANAGER = 30;

    /**
     * @var int
     */
    public const int ADMIN_AND_FINANCING_STAFF = 31;

    /**
     * @var int
     */
    public const int COLLECTION_STAFF = 32;

    /**
     * @var int
     */
    public const int DISTRIBUTION_STAFF = 33;
}
