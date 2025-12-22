<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Dskl
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int BPKH = 1;

    /**
     * @var int
     */
    public const int QURBAN = 2;

    /**
     * @var int
     */
    public const int FIDYAH = 3;
}
