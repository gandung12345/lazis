<?php

declare(strict_types=1);

namespace Schnell\Entity;

/**
 * @psalm-api
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
abstract class AbstractEntity implements EntityInterface
{
    /**
     * {@inheritDoc}
     */
    abstract public function getQueryBuilderAlias(): string;

    /**
     * {@inheritDoc}
     */
    abstract public function getCanonicalTableName(): string;

    /**
     * {@inheritDoc}
     */
    abstract public function getDqlName(): string;
}
