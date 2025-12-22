<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Scope
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int BRANCH = 1;

    /**
     * @var int
     */
    public const int BRANCH_REPRESENTATIVE = 2;

    /**
     * @var int
     */
    public const int TWIG = 3;
}
