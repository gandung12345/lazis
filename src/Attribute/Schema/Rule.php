<?php

declare(strict_types=1);

namespace Schnell\Attribute\Schema;

use Attribute;
use Schnell\Attribute\AttributeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class Rule implements AttributeInterface
{
    /**
     * @var bool
     */
    private $required;

    /**
     * @psalm-api
     *
     * @param bool $required
     * @return static
     */
    public function __construct(bool $required)
    {
        $this->setRequired($required);
    }

    /**
     * @psalm-api
     *
     * @return bool
     */
    public function getRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     * @return void
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return 'schema.rule';
    }
}
