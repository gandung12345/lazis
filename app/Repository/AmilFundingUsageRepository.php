<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Lazis\Api\Entity\AmilFundingUsage;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Schema\TransactionSchema;
use Lazis\Api\Schema\WalletSchema;
use Lazis\Api\Type\Transaction as TransactionType;
use Lazis\Api\Type\Wallet as WalletType;
use Schnell\Hydrator\ArrayHydrator;
use Schnell\Hydrator\MapHydrator;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class AmilFundingUsageRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new AmilFundingUsage());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new AmilFundingUsage());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new AmilFundingUsage(), $id);

        if (null === $entity) {
            return null;
        }

        return $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function create($refId, SchemaInterface $schema): EntityInterface|array|null
    {
        if ($schema->getAmount() > 0) {
            throw new RepositoryException(
                $this->getRequest(),
                "Amil funding usage amount must be equal or less than zero. " .
                "Because it's an outgoing transaction."
            );
        }

        $entityManager = $this->getMapper()->getEntityManager();
        $entityManager->getConnection()->beginTransaction();

        $amilFundingUsage = new AmilFundingUsage();
        $amilFundingUsage->setId(Uuid::v7()->toString());

        $amilFundingUsage = $this->getMapper()
            ->createReferenced(
                $refId,
                $schema,
                $amilFundingUsage,
                new Organization()
            );

        if (null === $amilFundingUsage) {
            $entityManager->getConnection()->rollBack();
            return null;
        }

        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::AMIL);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate($schema->getDate());
        $transactionSchema->setAmount($schema->getAmount());
        $transactionSchema->setType(TransactionType::OUTGOING);
        $transactionSchema->setDescription($schema->getDescription());
        $transactionSchema->setWallet($walletSchema);

        $transactionRepository = new TransactionRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        $transaction = $transactionRepository->create(
            $refId,
            $transactionSchema,
            false
        );

        if (null === $transaction) {
            $entityManager->getConnection()->rollBack();
            return null;
        }

        $transaction->setAmilFundingUsage($amilFundingUsage);

        $entityManager->flush();
        $entityManager->getConnection()->commit();

        return MapHydrator::create()->hydrate($amilFundingUsage);
    }

    /**
     * @param mixed $id
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function update($id, SchemaInterface $schema): EntityInterface|array|null
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->update($id, $schema, new AmilFundingUsage());

        return $result;
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function remove($id): EntityInterface|array|null
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->remove($id, new AmilFundingUsage());

        return $result;
    }
}
