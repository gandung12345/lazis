<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Wallet
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int ALMSGIVING = 1;

    /**
     * @var int
     */
    public const int NUCARE_SMART_DISBURSEMENT = 2;

    /**
     * @var int
     */
    public const int NUCARE_EMPOWERED_DISBURSEMENT = 3;

    /**
     * @var int
     */
    public const int NUCARE_HEALTH_DISBURSEMENT = 4;

    /**
     * @var int
     */
    public const int NUCARE_GREEN_DISBURSEMENT = 5;

    /**
     * @var int
     */
    public const int NUCARE_PEACE_DISBURSEMENT = 6;

    /**
     * @var int
     */
    public const int UNBOUNDED_DISBURSEMENT = 7;

    /**
     * @var int
     */
    public const int DONATION = 8;

    /**
     * @var int
     */
    public const int CAMPAIGN_PROGRAM = 9;

    /**
     * @var int
     */
    public const int NU_COIN = 10;

    /**
     * @var int
     */
    public const int AMIL = 11;

    /**
     * @var int
     */
    public const int ORGANIZATION_SOCIAL_FUNDING = 12;

    /**
     * @var int
     */
    public const int QURBAN = 13;

    /**
     * @var int
     */
    public const int NON_HALAL = 14;

    /**
     * @var int
     */
    public const int NU_COIN_AGGREGATOR = 15;
}
