<?php

declare(strict_types=1);

namespace Schnell\Config\Node;

use function get_class;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
abstract class AbstractNode implements NodeInterface
{
    /**
     * @var int
     */
    private int $type;

    /**
     * @var mixed
     */
    private mixed $value;

    /**
     * @var int
     */
    private int $column;

    /**
     * @var int
     */
    private int $line;

    /**
     * @psalm-api
     *
     * @param int $type
     * @param mixed $value
     * @param int $column
     * @param int $line
     * @return static
     */
    public function __construct(int $type, mixed $value, int $column, int $line)
    {
        $this->type = $type;
        $this->value = $value;
        $this->column = $column;
        $this->line = $line;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): int
    {
        return $this->type;
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
    public function getLineNumber(): int
    {
        return $this->line;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnNumber(): int
    {
        return $this->column;
    }
}
