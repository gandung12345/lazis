<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class OffBalanceSheet
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int ZAKAT_MAL = 1;

    /**
     * @var int
     */
    public const int ZAKAT_FITRAH = 2;

    /**
     * @var int
     */
    public const int INFAQ = 3;

    /**
     * @var int
     */
    public const int NATURA_INFAQ = 4;

    /**
     * @var int
     */
    public const int QURBAN = 5;

    /**
     * @var int
     */
    public const int FIDYAH = 6;

    /**
     * @var int
     */
    public const int DSKL = 7;
}
