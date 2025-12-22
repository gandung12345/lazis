<?php

declare(strict_types=1);

namespace Schnell\Config\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Block extends AbstractNode
{
    /**
     * @psalm-api
     *
     * @param mixed $value
     * @param int $col
     * @param int $line
     * @return static
     */
    public function __construct(mixed $value, int $col, int $line)
    {
        parent::__construct(NodeTypes::BLOCK, $value, $col, $line);
    }
}
