<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Throwable;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Donor;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\NuCoin;
use Lazis\Api\Entity\NuCoinBulkResponseState;
use Lazis\Api\Entity\NuCoinCrossTransferState;
use Lazis\Api\Entity\NuCoinResponseState;
use Lazis\Api\Entity\Volunteer;
use Lazis\Api\Entity\Wallet;
use Lazis\Api\Schema\NuCoinSchema;
use Lazis\Api\Schema\TransactionSchema;
use Lazis\Api\Schema\WalletSchema;
use Lazis\Api\Type\Transaction as TransactionType;
use Lazis\Api\Type\Wallet as WalletType;
use Lazis\Api\Repository\Exception\RepositoryException;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\EntityInterface;
use Schnell\Http\Code as HttpCode;
use Schnell\Hydrator\ArrayHydrator;
use Schnell\Hydrator\MapHydrator;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new NuCoin());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new NuCoin());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new NuCoin(), $id);

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
        if ($schema->getAmount() < 0) {
            throw new RepositoryException(
                $this->getRequest(),
                "NU coin transaction amount must be equal or greater than zero. " .
                "Because it's an incoming transaction."
            );
        }

        $donor = $this->getDonorByRefId($refId);

        if (null === $donor) {
            throw new RepositoryException(
                $this->getRequest(),
                sprintf("Donor with id '%s' not found.", $refId)
            );
        }

        $organization = $this->getOrganizationByDonor($donor);

        if (null === $organization) {
            return null;
        }

        $organizationRefId = $organization->getId();

        $entityManager = $this->getMapper()->getEntityManager();
        $entityManager->getConnection()->beginTransaction();

        $nuCoin = new NuCoin();
        $nuCoin->setId(Uuid::v7()->toString());

        $nuCoin = $this->getMapper()
            ->createReferenced(
                $refId,
                $schema,
                $nuCoin,
                $donor
            );

        if (null === $nuCoin) {
            $entityManager->getConnection()->rollBack();
            return null;
        }

        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::NU_COIN);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate($schema->getDate());
        $transactionSchema->setAmount($schema->getAmount());
        $transactionSchema->setType(TransactionType::INCOMING);
        $transactionSchema->setDescription('');
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

        $transaction->setNuCoin($nuCoin);

        $entityManager->flush();
        $entityManager->getConnection()->commit();

        return $hydrated === false
            ? $nuCoin
            : MapHydrator::create()->hydrate($nuCoin);
    }

    /**
     * @param array $schemas
     * @param bool $hydrated
     * @return array
     */
    public function createBulk(array $schemas, bool $hydrated = false): array
    {
        $result = [];

        foreach ($schemas as $key => $schema) {
            try {
                $nuCoin = $this->create(
                    $schema->getDonorId(),
                    new NuCoinSchema($schema->getDate(), $schema->getAmount()),
                    false
                );

                $result[] = $this->createNuCoinStateObject(
                    $key,
                    $nuCoin->getId(),
                    HttpCode::CREATED,
                    sprintf("NU coin created with id '%s'.", $nuCoin->getId())
                );
            } catch (Throwable $e) {
                $result[] = $this->createNuCoinStateObject(
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
     * @param array $schemas
     * @return array
     */
    public function createMultiple($refId, array $schemas): array
    {
        $result = [];

        foreach ($schemas as $index => $schema) {
            $object = $this->create($refId, $schema);
            $rstate = new NuCoinResponseState();

            $rstate->setIndex($index);
            $rstate->setId(null === $object ? '' : $object['id']);
            $rstate->setStatus(
                null === $object ? HttpCode::BAD_REQUEST : HttpCode::CREATED
            );

            $result[] = $rstate;
        }

        return ArrayHydrator::create()->hydrate($result);
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
            ->update($id, $schema, new NuCoin());

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
            ->remove($id, new NuCoin());

        return $result;
    }

    /**
     * @param mixed $refId
     * @return \Schnell\Entity\EntityInterface|null
     */
    private function getDonorByRefId($refId): ?EntityInterface
    {
        return $this->getMapper()->find(new Donor(), $refId);
    }

    /**
     * @param \Schnell\Entity\EntityInterface $child
     * @return \Schnell\Entity\EntityInterface|null
     */
    private function getOrganizationByDonor(EntityInterface $child): ?EntityInterface
    {
        $super = new Organization();
        $parent = new Volunteer();
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $results = $queryBuilder
            ->select($super->getQueryBuilderAlias())
            ->from($parent->getDqlName(), $parent->getQueryBuilderAlias())
            ->join(
                $child->getDqlName(),
                $child->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.volunteer',
                    $parent->getQueryBuilderAlias(),
                    $child->getQueryBuilderAlias()
                )
            )
            ->join(
                $super->getDqlName(),
                $super->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.organization',
                    $super->getQueryBuilderAlias(),
                    $parent->getQueryBuilderAlias()
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
    private function createNuCoinStateObject(
        int $index,
        string $id,
        int $status,
        string $message
    ): EntityInterface {
        $state = new NuCoinBulkResponseState();

        $state->setIndex($index);
        $state->setId($id);
        $state->setStatus($status);
        $state->setReason(HttpCode::toString($status));
        $state->setMessage($message);

        return $state;
    }
}
