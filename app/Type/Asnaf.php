<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Asnaf
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int FAKIR = 1;

    /**
     * @var int
     */
    public const int POOR = 2;

    /**
     * @var int
     */
    public const int AMIL = 3;

    /**
     * @var int
     */
    public const int MUALAF = 4;

    /**
     * @var int
     */
    public const int RIQAB = 5;

    /**
     * @var int
     */
    public const int GHARIM = 6;

    /**
     * @var int
     */
    public const int FISABILILLAH = 7;

    /**
     * @var int
     */
    public const int IBNU_SABIL = 8;
}
