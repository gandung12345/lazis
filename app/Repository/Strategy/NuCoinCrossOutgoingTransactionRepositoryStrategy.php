<?php

declare(strict_types=1);

namespace Lazis\Api\Repository\Strategy;

use Lazis\Api\Repository\Strategy\NuCoin\Outgoing\TwigToBranchRepresentative;
use Lazis\Api\Repository\Strategy\NuCoin\Outgoing\BranchRepresentativeToBranch;
use Lazis\Api\Type\NuCoinCrossTransactionOutgoingStrategy as NuCoinCrossTransactionOutgoingStrategyType;
use Schnell\Entity\EntityInterface;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinCrossOutgoingTransactionRepositoryStrategy extends AbstractRepositoryStrategy
{
    /**
     * @var int
     */
    private int $outgoingStrategyType;

    /**
     * @return int
     */
    public function getOutgoingStrategyType(): int
    {
        return $this->outgoingStrategyType;
    }

    /**
     * @param int $outgoingStrategyType
     * @return static
     */
    public function setOutgoingStrategyType(int $outgoingStrategyType): RepositoryStrategyInterface
    {
        $this->outgoingStrategyType = $outgoingStrategyType;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function invoke(SchemaInterface $schema): EntityInterface
    {
        $outgoingFlyweightObjects = [
            NuCoinCrossTransactionOutgoingStrategyType::TWIG_TO_BRANCH_REPRESENTATIVE => new TwigToBranchRepresentative(),
            NuCoinCrossTransactionOutgoingStrategyType::BRANCH_REPRESENTATIVE_TO_BRANCH => new BranchRepresentativeToBranch()
        ];

        $outgoingStrategyObject = $outgoingFlyweightObjects[
            $this->getOutgoingStrategyType()
        ];

        $outgoingStrategyObject->setMapper($this->getMapper());
        $outgoingStrategyObject->setRequest($this->getRequest());

        return $outgoingStrategyObject->transfer($schema);
    }
}
