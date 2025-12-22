<?php

declare(strict_types=1);

namespace Lazis\Api\Repository\Strategy\NuCoin\Outgoing;

use Lazis\Api\Repository\NuCoinCrossTransactionRecordRepository;
use Lazis\Api\Repository\Strategy\NuCoin\NuCoinConcreteStrategyInterface;
use Lazis\Api\Repository\Strategy\NuCoin\NuCoinRepositoryStrategyTrait;
use Lazis\Api\Schema\NuCoinCrossTransactionRecordSchema;
use Schnell\Entity\EntityInterface;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class TwigToBranchRepresentative implements NuCoinConcreteStrategyInterface
{
    use NuCoinRepositoryStrategyTrait;

    /**
     * {@inheritDoc}
     */
    public function transfer(SchemaInterface $schema): EntityInterface
    {
        $this->checkIfOrganizerAnAmil($schema);

        // 1. do cross organization transfer
        $crossTransferState = $this->transferCrossOrganizationToAggregator($schema);

        $nuCoinCrossTransactionRecordSchema = new NuCoinCrossTransactionRecordSchema();
        $nuCoinCrossTransactionRecordSchema->setDate($schema->getIssuedAt());
        $nuCoinCrossTransactionRecordSchema->setSourceId($schema->getSourceId());
        $nuCoinCrossTransactionRecordSchema->setSourceName($schema->getSourceName());
        $nuCoinCrossTransactionRecordSchema->setDestinationId($schema->getDestinationId());
        $nuCoinCrossTransactionRecordSchema->setDestinationName($schema->getDestinationName());
        $nuCoinCrossTransactionRecordSchema->setStatus($schema->getStatus());
        $nuCoinCrossTransactionRecordSchema->setType($schema->getType());
        $nuCoinCrossTransactionRecordSchema->setAmount($schema->getAmount());
        $nuCoinCrossTransactionRecordSchema->setProof($schema->getProof());

        $nuCoinCrossTransactionRecordRepository = new NuCoinCrossTransactionRecordRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        return $nuCoinCrossTransactionRecordRepository->create(
            $nuCoinCrossTransactionRecordSchema,
            false
        );
    }
}
