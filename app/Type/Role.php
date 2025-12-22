<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * This class contains generic RBAC type in route context.
 * No need to be fine-grained, although i want it to be :)
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Role
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int ROOT = 1;

    /**
     * @var int
     */
    public const int ADMIN = 2;

    /**
     * @var int
     */
    public const int ADMIN_MASTER_DATA = 3;

    /**
     * @var int
     */
    public const int AGGREGATOR_ADMIN = 4;

    /**
     * @var int
     */
    public const int TASHARUF_ADMIN = 5;
}
