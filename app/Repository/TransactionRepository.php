<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Transaction;
use Lazis\Api\Entity\Wallet;
use Lazis\Api\Schema\WalletMutationSchema;
use Lazis\Api\Type\Transaction as TransactionType;
use Lazis\Api\Repository\Exception\RepositoryException;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\EntityInterface;
use Schnell\Hydrator\ArrayHydrator;
use Schnell\Hydrator\MapHydrator;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class TransactionRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new Transaction());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new Transaction());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $refId
     * @return array
     */
    public function paginateByParent(EntityInterface $parent): array
    {
        $count = $this->getMapper()
            ->countByParent(new Transaction(), $parent);
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withHydrator(new ArrayHydrator())
            ->withPage($page)
            ->paginateByParent('wallet', $parent, new Transaction());

        return $result;
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new Transaction(), $id);

        if (null === $entity) {
            return null;
        }

        return $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param bool $hydrated
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function create(
        $refId,
        SchemaInterface $schema,
        bool $hydrated = true
    ): EntityInterface|array|null {
        $organization = $this->findOrganization($refId);

        if (null === $organization) {
            $this->doGracefulRollback();
            return null;
        }

        $wallet = $this->findWalletByType(
            $refId,
            $schema,
            new Organization(),
            new Wallet()
        );

        if (
            null === $wallet &&
            $schema->getType() === TransactionType::OUTGOING
        ) {
            $this->doGracefulRollback();

            throw new RepositoryException(
                $this->getRequest(),
                sprintf(
                    "Wallet with type %d was not found. " .
                    "This is an outgoing transaction.",
                    $schema->getWallet()->getType()
                )
            );
        }

        $difference = ($wallet === null
            ? 0
            : $wallet->getAmount()
        ) + $schema->getAmount();

        if (
            $schema->getType() === TransactionType::OUTGOING &&
            ($wallet->getAmount() === 0 || $difference < 0)
        ) {
            $this->doGracefulRollback();

            throw new RepositoryException(
                $this->getRequest(),
                sprintf(
                    "Transaction type is outgoing, but wallet with type " .
                    "%d is empty or have insufficient amount.",
                    $wallet->getType()
                )
            );
        }

        $wallet = null === $wallet
            ? $this->createWallet($organization, $schema)
            : $this->updateWallet($wallet, $schema);

        $transaction = new Transaction();
        $transaction->setId(Uuid::v7()->toString());

        $mapper = $hydrated
            ? $this->getMapper()->withHydrator(new MapHydrator())
            : $this->getMapper();

        $entity = $mapper->createReferenced(
            $wallet->getId(),
            $schema,
            $transaction,
            $wallet
        );

        $walletMutation = $this->createWalletMutation(
            $refId,
            $schema,
            $wallet
        );

        if (null === $walletMutation) {
            $this->doGracefulRollback();

            throw new RepositoryException(
                $this->getRequest(),
                sprintf(
                    "Failed to create wallet mutation for " .
                    "organization ID: %s, wallet ID: %s, " .
                    "and wallet type: %d\n",
                    $refId,
                    $wallet->getId(),
                    $wallet->getType()
                )
            );
        }

        return $entity;
    }

    /**
     * @param \Schnell\Entity\EntityInterface $entity
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface
     */
    private function createWallet(
        EntityInterface $entity,
        SchemaInterface $schema
    ): EntityInterface {
        $wallet = new Wallet();
        $wallet->setId(Uuid::v7()->toString());
        $wallet->setType($schema->getWallet()->getType());
        $wallet->setAmount($wallet->getAmount() + $schema->getAmount());
        $wallet->setOrganization($entity);

        $entityManager = $this->getMapper()->getEntityManager();
        $entityManager->persist($wallet);
        $entityManager->flush();

        return $wallet;
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|null
     */
    private function findOrganization($id): ?EntityInterface
    {
        $organization = new Organization();
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $results = $queryBuilder
            ->select($organization->getQueryBuilderAlias())
            ->from($organization->getDqlName(), $organization->getQueryBuilderAlias())
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $organization->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $id)
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        return sizeof($results) !== 1 ? null : $results[0];
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param \Schnell\Entity\EntityInterface $parent
     * @param \Schnell\Entity\EntityInterface $child
     * @return \Schnell\Entity\EntityInterface|null
     */
    private function findWalletByType(
        $refId,
        SchemaInterface $schema,
        EntityInterface $parent,
        EntityInterface $child
    ): ?EntityInterface {
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $query = $queryBuilder
            ->select($child->getQueryBuilderAlias())
            ->from($parent->getDqlName(), $parent->getQueryBuilderAlias())
            ->join(
                $child->getDqlName(),
                $child->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.organization',
                    $parent->getQueryBuilderAlias(),
                    $child->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq(
                    sprintf('%s.id', $parent->getQueryBuilderAlias()),
                    '?1'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.type', $child->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, $refId)
            ->setParameter(2, $schema->getWallet()->getType())
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery();

        if ($entityManager->getConnection()->isTransactionActive()) {
            $query->setLockMode(LockMode::PESSIMISTIC_READ);
        }

        $results = $query->getResult();

        if (sizeof($results) !== 1) {
            return null;
        }

        return $results[0];
    }

    /**
     * @param \Schnell\Entity\EntityInterface $entity
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface
     */
    private function updateWallet(
        EntityInterface $entity,
        SchemaInterface $schema
    ): EntityInterface {
        $entityManager = $this->getMapper()->getEntityManager();

        if ($entityManager->getConnection()->isTransactionActive()) {
            $entityManager->lock($entity, LockMode::PESSIMISTIC_WRITE);
        }

        $entity->setAmount($entity->getAmount() + $schema->getAmount());
        $entityManager->flush();
        return $entity;
    }

    /**
     * @internal
     *
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param \Schnell\Entity\EntityInterface $entity
     * @return \Schnell\Entity\EntityInterface|null
     */
    private function createWalletMutation(
        $refId,
        SchemaInterface $schema,
        EntityInterface $entity
    ): EntityInterface|null {
        $date = DateTimeDecorator::create()
            ->withFormat('Y');

        $walletMutationSchema = new WalletMutationSchema();

        $walletMutationSchema->setType($entity->getType());
        $walletMutationSchema->setAmount($schema->getAmount());
        $walletMutationSchema->setYear(intval($date->stringify()));

        $walletMutationRepository = new WalletMutationRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        return $walletMutationRepository->create($refId, $walletMutationSchema);
    }

    /**
     * @internal
     * @return void
     */
    private function doGracefulRollback(): void
    {
        $isTransactionActive = $this->getMapper()
            ->getEntityManager()
            ->getConnection()
            ->isTransactionActive();

        if ($isTransactionActive) {
            $this->getMapper()
                ->getEntityManager()
                ->getConnection()
                ->rollBack();
        }
    }
}
