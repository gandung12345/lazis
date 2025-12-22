<?php

declare(strict_types=1);

namespace Schnell\Attribute\Schema;

use Attribute;
use ReflectionClass;
use Schnell\Attribute\AttributeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class TransformedClassType implements AttributeInterface
{
    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var string
     */
    private $name;

    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var array
     */
    private $args;

    /**
     * @psalm-api
     *
     * @param string $name
     * @param array $args
     * @return static
     */
    public function __construct(string $name, array $args = [])
    {
        $this->setName($name);
        $this->setArgs($args);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     * @return void
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    /**
     * @param mixed $value
     * @return \Schnell\Attribute\AttributeInterface
     *
     * @psalm-api
     * @psalm-return \Schnell\Attribute\AttributeInterface
     */
    public function addArgs($value): AttributeInterface
    {
        $this->args[] = $value;
        return $this;
    }

    /**
     * @param mixed $value
     * @return \Schnell\Attribute\AttributeInterface
     *
     * @psalm-api
     * @psalm-return \Schnell\Attribute\AttributeInterface
     */
    public function addArgsImmutable($value): AttributeInterface
    {
        $cloned = clone $this;
        $cloned->args[] = $value;
        return $cloned;
    }

    /**
     * @psalm-api
     * @psalm-suppress ArgumentTypeCoercion
     *
     * @return object
     */
    public function getInstance(): object
    {
        $reflection = new ReflectionClass($this->getName());
        return $reflection->newInstanceArgs($this->getArgs());
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'schema.transformedClassType';
    }
}
