<?php

declare(strict_types=1);

namespace Lazis\Api\Http\Response;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface BuilderInterface
{
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setPair(string $key, mixed $value): void;

    /**
     * @param string $key
     * @param mixed $value
     * @return \Lazis\Api\Http\Response\BuilderInterface
     */
    public function withPair(string $key, mixed $value): BuilderInterface;

    /**
     * @return array
     */
    public function build(): array;
}
