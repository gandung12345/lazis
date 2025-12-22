<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Transaction
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int INCOMING = 1;

    /**
     * @var int
     */
    public const int OUTGOING = 2;
}
