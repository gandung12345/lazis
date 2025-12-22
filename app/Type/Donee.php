<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Donee
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int POOR = 1;

    /**
     * @var int
     */
    public const int ORPHAN = 2;

    /**
     * @var int
     */
    public const int QURAN_TEACHER = 3;

    /**
     * @var int
     */
    public const int DISABILITY = 4;

    /**
     * @var int
     */
    public const int OTHER = 5;
}
