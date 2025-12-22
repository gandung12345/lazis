<?php

declare(strict_types=1);

namespace Schnell\Attribute\Schema;

use Attribute;
use Schnell\Attribute\AttributeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class Regex implements AttributeInterface
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @psalm-api
     *
     * @param string $pattern
     * @return static
     */
    public function __construct(string $pattern)
    {
        $this->setPattern($pattern);
    }

    /**
     * @psalm-api
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     * @return void
     */
    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return 'schema.regex';
    }
}
