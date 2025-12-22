<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Lazis\Api\Repository\Exception\RepositoryException;
use Lazis\Api\Repository\Strategy\NuCoinCrossIncomingTransactionRepositoryStrategy;
use Lazis\Api\Repository\Strategy\NuCoinCrossOutgoingTransactionRepositoryStrategy;
use Lazis\Api\Repository\Strategy\RepositoryStrategyInvocatorInterface;
use Lazis\Api\Type\NuCoinCrossTransactionIncomingStrategy as NuCoinCrossTransactionIncomingStrategyType;
use Lazis\Api\Type\NuCoinCrossTransactionOutgoingStrategy as NuCoinCrossTransactionOutgoingStrategyType;
use Lazis\Api\Type\NuCoinCrossTransfer as NuCoinCrossTransferType;
use Lazis\Api\Type\Scope as ScopeType;
use Schnell\Entity\EntityInterface;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinCrossTransactionRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @var \Lazis\Api\Repository\Strategy\RepositoryStrategyInvocatorInterface|null
     */
    private ?RepositoryStrategyInvocatorInterface $repositoryStrategyInvocator;

    /**
     * @return \Lazis\Api\Repository\Strategy\RepositoryStrategyInvocatorInterface|null
     */
    public function getRepositoryStrategyInvocator(): ?RepositoryStrategyInvocatorInterface
    {
        return $this->repositoryStrategyInvocator;
    }

    /**
     * @param \Lazis\Api\Repository\Strategy\RepositoryStrategyInvocatorInterface|null $repositoryStrategyInvocator
     * @return void
     */
    public function setRepositoryStrategyInvocator(
        ?RepositoryStrategyInvocatorInterface $repositoryStrategyInvocator
    ): void {
        $this->repositoryStrategyInvocator = $repositoryStrategyInvocator;
    }

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|null
     */
    public function createTransaction(SchemaInterface $schema): ?EntityInterface
    {
        if ($schema->getStatus() !== NuCoinCrossTransferType::APPROVED) {
            throw new RepositoryException(
                $this->getRequest(),
                "Transaction can be done if transaction queue state is approved."
            );
        }

        $this->buildStrategyFromSchema($schema);
        return $this->getRepositoryStrategyInvocator()->invoke($schema);
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return void
     */
    private function buildStrategyFromSchema(SchemaInterface $schema): void
    {
        if ($this->getRepositoryStrategyInvocator() === null) {
            throw new RepositoryException(
                $this->getRequest(),
                "Repository strategy invocator object is null."
            );
        }

        $organizationRepository = new OrganizationRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        $source = $organizationRepository->getById($schema->getSourceId());
        $destination = $organizationRepository->getById($schema->getDestinationId());

        if (null === $source || null === $destination) {
            throw new RepositoryException(
                $this->getRequest(),
                "Both of source and destination organization ID must be exists and must be in " .
                "valid UUID format."
            );
        }

        $strategyObject = null;
        $strategyObjectFlyweight = [
            new NuCoinCrossIncomingTransactionRepositoryStrategy(),
            new NuCoinCrossOutgoingTransactionRepositoryStrategy()
        ];

        if (
            $source->getScope() === ScopeType::BRANCH &&
            $destination->getScope() === ScopeType::BRANCH_REPRESENTATIVE
        ) {
            $strategyObject = $strategyObjectFlyweight[0]
                ->setIncomingStrategyType(NuCoinCrossTransactionIncomingStrategyType::BRANCH_REPRESENTATIVE_FROM_BRANCH);
        }

        if (
            $source->getScope() === ScopeType::BRANCH_REPRESENTATIVE &&
            $destination->getScope() === ScopeType::TWIG
        ) {
            $strategyObject = $strategyObjectFlyweight[0]
                ->setIncomingStrategyType(NuCoinCrossTransactionIncomingStrategyType::TWIG_FROM_BRANCH_REPRESENTATIVE);
        }

        if (
            $source->getScope() === ScopeType::BRANCH_REPRESENTATIVE &&
            $destination->getScope() === ScopeType::BRANCH
        ) {
            $strategyObject = $strategyObjectFlyweight[1]
                ->setOutgoingStrategyType(NuCoinCrossTransactionOutgoingStrategyType::BRANCH_REPRESENTATIVE_TO_BRANCH);
        }

        if (
            $source->getScope() === ScopeType::TWIG &&
            $destination->getScope() === ScopeType::BRANCH_REPRESENTATIVE
        ) {
            $strategyObject = $strategyObjectFlyweight[1]
                ->setOutgoingStrategyType(NuCoinCrossTransactionOutgoingStrategyType::TWIG_TO_BRANCH_REPRESENTATIVE);
        }

        if (null === $strategyObject) {
            throw new RepositoryException(
                $this->getRequest(),
                "Invalid organization scope-pair direction."
            );
        }

        $strategyObject->setMapper($this->getMapper());
        $strategyObject->setRequest($this->getRequest());

        $this->getRepositoryStrategyInvocator()->setRepositoryStrategy($strategyObject);
    }
}
