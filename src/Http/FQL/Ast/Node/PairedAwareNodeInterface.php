<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Ast\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface PairedAwareNodeInterface extends NodeInterface
{
    /**
     * @psalm-api
     *
     * @return string|null
     */
    public function getKey(): ?string;

    /**
     * @param string|null $key
     * @return void
     */
    public function setKey(?string $key): void;

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
    public function setValue(mixed $value): void;
}
