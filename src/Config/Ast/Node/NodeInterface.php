<?php

declare(strict_types=1);

namespace Schnell\Config\Ast\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface NodeInterface
{
    /**
     * @psalm-api
     *
     * @return int
     */
    public function getType(): int;

    /**
     * @psalm-api
     *
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function __toString(): string;
}
