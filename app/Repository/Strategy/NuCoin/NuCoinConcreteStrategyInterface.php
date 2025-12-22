<?php

declare(strict_types=1);

namespace Lazis\Api\Repository\Strategy\NuCoin;

use Lazis\Api\Repository\Strategy\ConcreteRepositoryStrategyAwareInterface;
use Schnell\Entity\EntityInterface;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface NuCoinConcreteStrategyInterface extends ConcreteRepositoryStrategyAwareInterface
{
    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface
     */
    public function transfer(SchemaInterface $schema): EntityInterface;
}
