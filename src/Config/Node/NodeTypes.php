<?php

declare(strict_types=1);

namespace Schnell\Config\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface NodeTypes
{
    /**
     * @var int
     */
    public const int BLOCK = 1;

    /**
     * @var int
     */
    public const int IDENTIFIER = 2;

    /**
     * @var int
     */
    public const int INTEGER = 4;

    /**
     * @var int
     */
    public const int STRING = 8;

    /**
     * @var int
     */
    public const int ASSIGN = 16;

    /**
     * @var int
     */
    public const int ARRAY = 32;

    /**
     * @var int
     */
    public const int BOOLEAN = 64;
}
