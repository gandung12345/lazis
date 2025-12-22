<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface LiteralNodeInterface extends NodeInterface
{
    /**
     * @psalm-api
     *
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     * @return void
     */
    public function setValue($value): void;
}
