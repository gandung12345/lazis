<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Ast\Node\Literal;

use Schnell\Http\FQL\Ast\AstInterface;
use Schnell\Http\FQL\Ast\Node\NodeTypes as AstNodeTypes;
use Schnell\Http\FQL\Ast\Node\PairedAwareNodeInterface;

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class KeyValue implements PairedAwareNodeInterface
{
    /**
     * @var string|null
     */
    private ?string $key;

    /**
     * @var mixed
     */
    private mixed $value;

    /**
     * @psalm-api
     *
     * @param string|null $key
     * @param mixed $value
     * @return static
     */
    public function __construct(?string $key = null, mixed $value = null)
    {
        $this->setKey($key);
        $this->setValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setKey(?string $key): void
    {
        $this->key = $key;
    }

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

    /**
     * {@inheritdoc}
     */
    public function getType(): int
    {
        return AstNodeTypes::KEY_VALUE;
    }

    /**
     * {@inheritdoc}
     */
    public function getInvokable(AstInterface $ast): ?array
    {
        return null;
    }
}
