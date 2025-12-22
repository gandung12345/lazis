<?php

declare(strict_types=1);

namespace Schnell\Attribute\Schema;

use Attribute;
use Schnell\Attribute\AttributeInterface;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class ChainEnum implements AttributeInterface
{
    /**
     * @var string
     */
    private string $field;

    /**
     * @var array
     */
    private array $value;

    /**
     * @psalm-api
     *
     * @param string $field
     * @param array $value
     * @return static
     */
    public function __construct(string $field, array $value)
    {
        $this->setField($field);
        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     * @return void
     */
    public function setField(string $field): void
    {
        $this->field = $field;
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        return $this->value;
    }

    /**
     * @param array $value
     * @return void
     */
    public function setValue(array $value): void
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {
        return 'schema.chainEnum';
    }
}
