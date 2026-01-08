<?php

declare(strict_types=1);

namespace Lazis\Api\Repository\Strategy;

use Lazis\Api\Repository\Strategy\NuCoin\Incoming\TwigFromBranchRepresentative;
use Lazis\Api\Repository\Strategy\NuCoin\Incoming\BranchRepresentativeFromBranch;
use Lazis\Api\Type\NuCoinCrossTransactionIncomingStrategy as NuCoinCrossTransactionIncomingStrategyType;
use Schnell\Entity\EntityInterface;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinCrossIncomingTransactionRepositoryStrategy extends AbstractRepositoryStrategy
{
    /**
     * @var int
     */
    private int $incomingStrategyType;

    /**
     * @return int
     */
    public function getIncomingStrategyType(): int
    {
        return $this->incomingStrategyType;
    }

    /**
     * @param int $incomingStrategyType
     * @return static
     */
    public function setIncomingStrategyType(int $incomingStrategyType): RepositoryStrategyInterface
    {
        $this->incomingStrategyType = $incomingStrategyType;
        return $this;
    }

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface
     */
    public function invoke(SchemaInterface $schema): EntityInterface
    {
        $incomingFlyweightObjects = [
            NuCoinCrossTransactionIncomingStrategyType::TWIG_FROM_BRANCH_REPRESENTATIVE => new TwigFromBranchRepresentative(),
            NuCoinCrossTransactionIncomingStrategyType::BRANCH_REPRESENTATIVE_FROM_BRANCH => new BranchRepresentativeFromBranch()
        ];

        $incomingStrategyObject = $incomingFlyweightObjects[
            $this->getIncomingStrategyType()
        ];

        $incomingStrategyObject->setMapper($this->getMapper());
        $incomingStrategyObject->setRequest($this->getRequest());

        return $incomingStrategyObject->transfer($schema);
    }

    /**
     * {@inheritDoc}
     */
    public function repositoryStrategyId(): string
    {
        return 'nu-coin-incoming-transaction';
    }
}
