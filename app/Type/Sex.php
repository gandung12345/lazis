<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Sex
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int MALE = 1;

    /**
     * @var int
     */
    public const int FEMALE = 2;
}
