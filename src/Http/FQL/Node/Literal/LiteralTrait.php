<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Node\Literal;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
trait LiteralTrait
{
    /**
     * @param mixed
     */
    private mixed $value;

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }
}
