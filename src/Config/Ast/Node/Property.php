<?php

declare(strict_types=1);

namespace Schnell\Config\Ast\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Property extends AbstractNode
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @psalm-api
     *
     * @param string $property
     * @param mixed $value
     * @return static
     */
    public function __construct(string $property, $value)
    {
        $this->property = $property;
        $this->value    = $value;
        $this->type     = NodeTypes::PROPERTY;
    }

    /**
     * @psalm-api
     *
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @psalm-api
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return '(property)';
    }
}
