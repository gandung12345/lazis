<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class BranchRepresentativePosition
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int RAIS = 22;

    /**
     * @var int
     */
    public const int LEADER = 23;

    /**
     * @var int
     */
    public const int AREA_MANAGER = 24;

    /**
     * @var int
     */
    public const int ADMIN_AND_FINANCING_STAFF = 25;

    /**
     * @var int
     */
    public const int COLLECTION_STAFF = 26;

    /**
     * @var int
     */
    public const int DISTRIBUTION_STAFF = 27;
}
