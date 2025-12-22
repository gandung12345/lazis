<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Throwable;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Donor;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\NuCoinAggregator;
use Lazis\Api\Entity\NuCoinAggregatorCrossWalletFaultResponse;
use Lazis\Api\Entity\NuCoinAggregatorCrossWalletSuccessResponse;
use Lazis\Api\Entity\Volunteer;
use Lazis\Api\Entity\Wallet;
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
class NuCoinAggregatorRepository extends AbstractRepository
{
    use RepositoryTrait;

    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new NuCoinAggregator());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new NuCoinAggregator());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    public function getById($id): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new NuCoinAggregator(), $id);

        if (null === $entity) {
            return null;
        }

        return $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

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

        $nuCoinAggregator = new NuCoinAggregator();
        $nuCoinAggregator->setId(Uuid::v7()->toString());

        $nuCoinAggregator = $this->getMapper()
            ->createReferenced(
                $refId,
                $schema,
                $nuCoinAggregator,
                $donor
            );

        if (null === $nuCoinAggregator) {
            $entityManager->getConnection()->rollBack();
            return null;
        }

        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::NU_COIN_AGGREGATOR);

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

        $transaction->setNuCoinAggregator($nuCoinAggregator);

        $entityManager->flush();
        $entityManager->getConnection()->commit();

        return $hydrated === false
            ? $nuCoinAggregator
            : MapHydrator::create()->hydrate($nuCoinAggregator);
    }

    public function update($id, SchemaInterface $schema): EntityInterface|array|null
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->update($id, $schema, new NuCoinAggregator());

        return $result;
    }

    public function remove($id): EntityInterface|array|null
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->remove($id, new NuCoinAggregator());

        return $result;
    }

    public function moveFund(SchemaInterface $schema): EntityInterface|array|null
    {
        $aggregatorFundAmount = $this->getAggregatorFundAmount($schema);

        if ($aggregatorFundAmount instanceof EntityInterface) {
            return $aggregatorFundAmount;
        }

        $status = $this->nullifyAggregatorWallet($schema, $aggregatorFundAmount);

        if ($status instanceof EntityInterface) {
            return $status;
        }

        $status = $this->doMoveFund($schema, $aggregatorFundAmount);

        if ($status instanceof EntityInterface) {
            return $status;
        }

        return new NuCoinAggregatorCrossWalletSuccessResponse(
            HttpCode::OK,
            $schema->getOrganizationId(),
            $aggregatorFundAmount
        );
    }

    private function getAggregatorFundAmount(SchemaInterface $schema): int|EntityInterface
    {
        $entities = [new Organization(), new Wallet()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder
            ->select($entities[1]->getQueryBuilderAlias())
            ->from($entities[0]->getDqlName(), $entities[0]->getQueryBuilderAlias())
            ->join(
                $entities[1]->getDqlName(),
                $entities[1]->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.organization',
                    $entities[0]->getQueryBuilderAlias(),
                    $entities[1]->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq(
                    sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                    '?1'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.type', $entities[1]->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, WalletType::NU_COIN_AGGREGATOR)
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (sizeof($result) === 0) {
            return new NuCoinAggregatorCrossWalletFaultResponse(
                HttpCode::NOT_FOUND,
                sprintf(
                    "NU coin aggregator wallet with oid: %s not found",
                    $schema->getOrganizationId()
                )
            );
        }

        return $result[0]->getAmount();
    }

    private function nullifyAggregatorWallet(
        SchemaInterface $schema,
        int $totalAmount
    ): EntityInterface|bool {
        $transactionRepository = new TransactionRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        // perform amount reduction on NU coin aggregator wallet
        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::NU_COIN_AGGREGATOR);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate(new DateTimeDecorator());
        $transactionSchema->setAmount($totalAmount * -1);
        $transactionSchema->setType(TransactionType::OUTGOING);
        $transactionSchema->setDescription(
            sprintf(
                '(nu-coin::ambil-dari-aggregator-wallet): Penarikan ' .
                'data sejumlah %d dari wallet dibawah organisasi (id: ' .
                '%s) berhasil.',
                $totalAmount,
                $schema->getOrganizationId()
            )
        );

        $transactionSchema->setWallet($walletSchema);

        $transaction = $transactionRepository->create(
            $schema->getOrganizationId(),
            $transactionSchema,
            false
        );

        if (null === $transaction) {
            return new NuCoinAggregatorCrossWalletFaultResponse(
                HttpCode::INTERNAL_SERVER_ERROR,
                sprintf(
                    'Outgoing transaction from nu coin aggregator wallet (id: ' .
                    '%s, amount: %d) failed.',
                    $schema->getOrganizationId(),
                    $totalAmount
                )
            );
        }

        return true;
    }

    private function doMoveFund(
        SchemaInterface $schema,
        int $totalAmount
    ): EntityInterface|bool {
        // perform amount addition on NU coin wallet
        $transactionRepository = new TransactionRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::NU_COIN);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate(new DateTimeDecorator());
        $transactionSchema->setAmount($totalAmount);
        $transactionSchema->setType(TransactionType::INCOMING);
        $transactionSchema->setDescription(
            sprintf(
                '(nu-coin::transfer-dari-banyak-organisasi): Terima dana sebesar ' .
                '%d ke dompet NU coin untuk organisasi dengan ID (%s) berhasil.',
                $totalAmount,
                $schema->getOrganizationId()
            )
        );

        $transactionSchema->setWallet($walletSchema);

        $transaction = $transactionRepository->create(
            $schema->getOrganizationId(),
            $transactionSchema,
            false
        );

        if (null === $transaction) {
            return new NuCoinAggregatorCrossWalletFaultResponse(
                HttpCode::INTERNAL_SERVER_ERROR,
                sprintf(
                    'Incoming transaction to NU coin wallet (id: ' .
                    '%s, amount: %d) failed.',
                    $schema->getOrganizationId(),
                    $totalAmount
                )
            );
        }

        return true;
    }

    private function getDonorByRefId($refId): ?EntityInterface
    {
        return $this->getMapper()->find(new Donor(), $refId);
    }

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
}
