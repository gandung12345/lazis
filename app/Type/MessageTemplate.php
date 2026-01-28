<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class MessageTemplate
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int ZAKAT_MAAL = 1;

    /**
     * @var int
     */
    public const int ZAKAT_FITRAH = 2;

    /**
     * @var int
     */
    public const int NU_COIN = 3;

    /**
     * @var int
     */
    public const int INFAQ = 4;
}
