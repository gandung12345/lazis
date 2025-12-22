<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Muzakki
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int PERSONAL = 1;

    /**
     * @var int
     */
    public const int COLLECTIVE = 2;
}
