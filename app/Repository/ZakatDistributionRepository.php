<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Throwable;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Donee;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\ZakatDistribution;
use Lazis\Api\Entity\ZakatDistributionBulkResponseState;
use Lazis\Api\Repository\Exception\RepositoryException;
use Lazis\Api\Schema\TransactionSchema;
use Lazis\Api\Schema\WalletSchema;
use Lazis\Api\Schema\ZakatDistributionSchema;
use Lazis\Api\Type\Transaction as TransactionType;
use Lazis\Api\Type\Wallet as WalletType;
use Schnell\Entity\EntityInterface;
use Schnell\Http\Code as HttpCode;
use Schnell\Hydrator\ArrayHydrator;
use Schnell\Hydrator\MapHydrator;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ZakatDistributionRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new ZakatDistribution());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new ZakatDistribution());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new ZakatDistribution(), $id);

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
        if ($schema->getAmount() > 0) {
            throw new RepositoryException(
                $this->getRequest(),
                "Zakat distribution amount must be equal or less than zero. " .
                "Because it's an outgoing transaction."
            );
        }

        $donee = $this->getDoneeByRefId($refId);

        if (null === $donee) {
            throw new RepositoryException(
                $this->getRequest(),
                sprintf('Donee with id \'%s\' not found.', $refId)
            );
        }

        $organization = $this->getOrganizationByDonee($donee);

        if (null === $organization) {
            return null;
        }

        $organizationRefId = $organization->getId();

        $entityManager = $this->getMapper()->getEntityManager();
        $entityManager->getConnection()->beginTransaction();

        $zakatDistribution = new ZakatDistribution();
        $zakatDistribution->setId(Uuid::v7()->toString());

        $zakatDistribution = $this->getMapper()
            ->createReferenced(
                $refId,
                $schema,
                $zakatDistribution,
                new Donee()
            );

        if (null === $zakatDistribution) {
            $entityManager->getConnection()->rollBack();
            return null;
        }

        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::ALMSGIVING);

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
            $organizationRefId,
            $transactionSchema,
            false
        );

        if (null === $transaction) {
            $entityManager->getConnection()->rollBack();
            return null;
        }

        $transaction->setZakatDistribution($zakatDistribution);

        $entityManager->flush();
        $entityManager->getConnection()->commit();

        return false === $hydrated
            ? $zakatDistribution
            : MapHydrator::create()->hydrate($zakatDistribution);
    }

    /**
     * @param array $schemas
     * @param bool $hydrated
     * @return array
     */
    public function createBulk(array $schemas, bool $hydrated = true): array
    {
        $result = [];

        foreach ($schemas as $key => $schema) {
            try {
                $zakatDistribution = $this->create(
                    $schema->getDoneeId(),
                    new ZakatDistributionSchema(
                        $schema->getDate(),
                        $schema->getProgram(),
                        $schema->getAmount(),
                        $schema->getDescription(),
                        ''
                    ),
                    false
                );

                $result[] = $this->createZakatDistributionStateObject(
                    $key,
                    $zakatDistribution->getId(),
                    HttpCode::CREATED,
                    sprintf(
                        'Zakat distribution created with id \'%s\'.',
                        $zakatDistribution->getId()
                    )
                );
            } catch (Throwable $e) {
                $result[] = $this->createZakatDistributionStateObject(
                    $key,
                    '',
                    HttpCode::UNPROCESSABLE_ENTITY,
                    $e->getMessage()
                );
            }
        }

        return false === $hydrated
            ? $result
            : ArrayHydrator::create()->hydrate($result);
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
            ->update($id, $schema, new ZakatDistribution());

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
            ->remove($id, new ZakatDistribution());

        return $result;
    }

    /**
     * @param mixed $refId
     * @return \Schnell\Entity\EntityInterface|null
     */
    private function getDoneeByRefId($refId): ?EntityInterface
    {
        return $this->getMapper()->find(new Donee(), $refId);
    }

    /**
     * @param \Schnell\Entity\EntityInterface $child
     * @return \Schnell\Entity\EntityInterface|null
     */
    private function getOrganizationByDonee(EntityInterface $child): ?EntityInterface
    {
        $parent = new Organization();
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $results = $queryBuilder
            ->select($parent->getQueryBuilderAlias())
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
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $child->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $child->getId())
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (sizeof($results) !== 1) {
            return null;
        }

        return $results[0];
    }

    /**
     * @param int $index
     * @param string $id
     * @param int $status
     * @param string $message
     * @return \Schnell\Entity\EntityInterface
     */
    public function createZakatDistributionStateObject(
        int $index,
        string $id,
        int $status,
        string $message
    ): EntityInterface {
        $state = new ZakatDistributionBulkResponseState();

        $state->setIndex($index);
        $state->setId($id);
        $state->setStatus($status);
        $state->setReason(HttpCode::toString($status));
        $state->setMessage($message);

        return $state;
    }
}
