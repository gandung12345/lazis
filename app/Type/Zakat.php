<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Zakat
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int FITRAH = 1;

    /**
     * @var int
     */
    public const int MAAL = 2;
}
