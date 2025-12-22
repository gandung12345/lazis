<?php

declare(strict_types=1);

namespace Lazis\Api\Repository\Strategy;

use Schnell\Entity\EntityInterface;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface RepositoryStrategyInterface extends ConcreteRepositoryStrategyAwareInterface
{
    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface
     */
    public function invoke(SchemaInterface $schema): EntityInterface;
}
