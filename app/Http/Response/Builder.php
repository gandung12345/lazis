<?php

declare(strict_types=1);

namespace Lazis\Api\Http\Response;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Builder implements BuilderInterface
{
    /**
     * @var array
     */
    private array $list = [];

    /**
     * {@inheritDoc}
     */
    public function setPair(string $key, mixed $value): void
    {
        $this->list[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function withPair(string $key, mixed $value): BuilderInterface
    {
        $ret = clone $this;
        $ret->setPair($key, $value);
        return $ret;
    }

    /**
     * {@inheritDoc}
     */
    public function build(): array
    {
        return $this->list;
    }
}
