<?php

declare(strict_types=1);

namespace Schnell\Config\Ast\Node;

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
    public const int PROPERTY = 2;

    /**
     * @var int
     */
    public const int ROOT = 255;
}
