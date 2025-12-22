<?php

declare(strict_types=1);

namespace Schnell\Config\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface NodeInterface
{
    /**
     * @return int
     */
    public function getType(): int;

    /**
     * @psalm-api
     *
     * @return mixed
     */
    public function getValue();

    /**
     * @return int
     */
    public function getLineNumber(): int;

    /**
     * @return int
     */
    public function getColumnNumber(): int;
}
