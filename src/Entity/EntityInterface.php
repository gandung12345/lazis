<?php

declare(strict_types=1);

namespace Schnell\Entity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface EntityInterface
{
    /**
     * @psalm-api
     *
     * @return string
     */
    public function getQueryBuilderAlias(): string;

    /**
     * @psalm-api
     *
     * @return string
     */
    public function getCanonicalTableName(): string;

    /**
     * @psalm-api
     *
     * @return string
     */
    public function getDqlName(): string;
}
