<?php

declare(strict_types=1);

namespace Lazis\Api\Repository\Strategy\NuCoin;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\NuCoinCrossTransferState;
use Lazis\Api\Entity\Amil;
use Lazis\Api\Entity\Organizer;
use Lazis\Api\Entity\Wallet;
use Lazis\Api\Repository\OrganizationRepository;
use Lazis\Api\Repository\TransactionRepository;
use Lazis\Api\Repository\Exception\RepositoryException;
use Lazis\Api\Schema\TransactionSchema;
use Lazis\Api\Schema\WalletSchema;
use Lazis\Api\Type\Transaction as TransactionType;
use Lazis\Api\Type\Wallet as WalletType;
use Psr\Http\Message\RequestInterface;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\EntityInterface;
use Schnell\Http\Code as HttpCode;
use Schnell\Hydrator\MapHydrator;
use Schnell\Mapper\MapperInterface;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
trait NuCoinRepositoryStrategyTrait
{
    /**
     * @var \Schnell\Mapper\MapperInterface|null
     */
    private ?MapperInterface $mapper;

    /**
     * @var \Psr\Http\Message\RequestInterface|null
     */
    private ?RequestInterface $request;

    /**
     * @return \Schnell\Mapper\MapperInterface|null
     */
    public function getMapper(): ?MapperInterface
    {
        return $this->mapper;
    }

    /**
     * @param \Schnell\Mapper\MapperInterface|null $mapper
     * @return void
     */
    public function setMapper(?MapperInterface $mapper): void
    {
        $this->mapper = $mapper;
    }

    /**
     * @return \Psr\Http\Message\RequestInterface|null
     */
    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface|null $request
     * @return void
     */
    public function setRequest(?RequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface
     */
    private function getAmilByOrganizerId(SchemaInterface $schema): EntityInterface
    {
        $entities = [new Organizer(), new Amil()];
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
                    '%s.id = %s.organizer',
                    $entities[0]->getQueryBuilderAlias(),
                    $entities[1]->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $schema->getOrganizerId())
            ->getQuery()
            ->getResult();

        return $result[0];
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return bool
     */
    private function isOrganizerAnAmil(SchemaInterface $schema): bool
    {
        $entities = [new Organizer(), new Amil()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $count = $queryBuilder
            ->select(
                sprintf(
                    'count(%s)',
                    $entities[1]->getQueryBuilderAlias()
                )
            )
            ->from($entities[0]->getDqlName(), $entities[0]->getQueryBuilderAlias())
            ->join(
                $entities[1]->getDqlName(),
                $entities[1]->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.organizer',
                    $entities[0]->getQueryBuilderAlias(),
                    $entities[1]->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $schema->getOrganizerId())
            ->getQuery()
            ->getSingleScalarResult();

        return $count === 1 ? true : false;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return void
     */
    private function checkIfOrganizerAnAmil(SchemaInterface $schema): void
    {
        if ($this->isOrganizerAnAmil($schema) === false) {
            throw new RepositoryException(
                $this->getRequest(),
                sprintf(
                    'Organizer with id: %s is not an amil.',
                    $schema->getOrganizerId()
                )
            );
        }

        return;
    }

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param bool $moveFund
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function transferCrossOrganization(
        SchemaInterface $schema,
        bool $moveFund = false
    ): EntityInterface|array|null {
        $organizationRepository = new OrganizationRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        $srcOrg = $organizationRepository->getById($schema->getSourceId());
        $destOrg = $organizationRepository->getById($schema->getDestinationId());

        if (null === $srcOrg || null === $destOrg) {
            return null;
        }

        if ($moveFund) {
            $this->moveFundFromAggregatorToNuCoin($schema, $srcOrg);
        }

        return $this->doTransferCrossOrganization($schema, $srcOrg, $destOrg);
    }

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function transferCrossOrganizationToAggregator(
        SchemaInterface $schema
    ): EntityInterface|array|null {
        $organizationRepository = new OrganizationRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        $srcOrg = $organizationRepository->getById($schema->getSourceId());
        $destOrg = $organizationRepository->getById($schema->getDestinationId());

        if (null === $srcOrg || null === $destOrg) {
            return null;
        }

        return $this->doTransferCrossOrganizationToAggregator($schema, $srcOrg, $destOrg);
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param \Schnell\Entity\EntityInterface $source
     * @param \Schnell\Entity\EntityInterface $destination
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    private function doTransferCrossOrganization(
        SchemaInterface $schema,
        EntityInterface $source,
        EntityInterface $destination
    ): EntityInterface|array|null {
        $wallet = $this->getWalletByOrganization($source, WalletType::NU_COIN);

        if (null === $wallet) {
            throw new RepositoryException(
                $this->getRequest(),
                sprintf(
                    "NU coin wallet under organization ID: '%s' not found",
                    $source->getId()
                )
            );
        }

        if ($schema->getAmount() > $wallet->getAmount()) {
            throw new RepositoryException(
                $this->getRequest(),
                "Requested amount is bigger than in the wallet."
            );
        }

        $transactionRepository = new TransactionRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        // perform amount reduction on NU coin source wallet
        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::NU_COIN);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate(new DateTimeDecorator());
        $transactionSchema->setAmount($schema->getAmount() * -1);
        $transactionSchema->setType(TransactionType::OUTGOING);
        $transactionSchema->setDescription(
            sprintf(
                '(nu-coin::ambil-dari-wallet): Penarikan dana ' .
                'sejumlah %d dari wallet dibawah organisasi (nama: ' .
                '%s, id: %s) berhasil.',
                $schema->getAmount(),
                $source->getName(),
                $source->getId()
            )
        );
        $transactionSchema->setWallet($walletSchema);

        $transaction = $transactionRepository->create(
            $source->getId(),
            $transactionSchema,
            false
        );

        if (null === $transaction) {
            return null;
        }

        // perform amount addition on NU coin destination wallet
        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::NU_COIN);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate($schema->getIssuedAt());
        $transactionSchema->setAmount($schema->getAmount());
        $transactionSchema->setType(TransactionType::INCOMING);
        $transactionSchema->setDescription(
            sprintf(
                '(nu-coin::transfer-antar-organisasi): Kirim dana ' .
                'sejumlah %d ke wallet dibawah organisasi (nama: ' .
                '%s, id: %s) berhasil.',
                $schema->getAmount(),
                $destination->getName(),
                $destination->getId()
            )
        );
        $transactionSchema->setWallet($walletSchema);

        $transaction = $transactionRepository->create(
            $destination->getId(),
            $transactionSchema,
            false
        );

        if (null === $transaction) {
            return null;
        }

        $transferState = new NuCoinCrossTransferState();
        $transferState->setSource($source->getId());
        $transferState->setDestination($destination->getId());
        $transferState->setAmount($schema->getAmount());
        $transferState->setStatusCode(HttpCode::CREATED);

        return MapHydrator::create()->hydrate($transferState);
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param \Schnell\Entity\EntityInterface $source
     * @param \Schnell\Entity\EntityInterface $destination
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    private function doTransferCrossOrganizationToAggregator(
        SchemaInterface $schema,
        EntityInterface $source,
        EntityInterface $destination
    ): EntityInterface|array|null {
        $wallet = $this->getWalletByOrganization($source, WalletType::NU_COIN_AGGREGATOR);

        if (null === $wallet) {
            throw new RepositoryException(
                $this->getRequest(),
                sprintf(
                    "NU coin aggregator wallet under organization ID: '%s' not found",
                    $source->getId()
                )
            );
        }

        if ($schema->getAmount() > $wallet->getAmount()) {
            throw new RepositoryException(
                $this->getRequest(),
                "Requested amount is bigger than in the wallet."
            );
        }

        $transactionRepository = new TransactionRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        // perform amount reduction on NU coin source wallet.
        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::NU_COIN_AGGREGATOR);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate(new DateTimeDecorator());
        $transactionSchema->setAmount($schema->getAmount() * -1);
        $transactionSchema->setType(TransactionType::OUTGOING);
        $transactionSchema->setDescription(
            // previous: transfer-antar-organisasi
            sprintf(
                '(nu-coin-aggregator::ambil-dari-wallet): Penarikan dana ' .
                'sejumlah %d dari wallet dibawah organisasi (nama: ' .
                '%s, id: %s) berhasil.',
                $schema->getAmount(),
                $source->getName(),
                $source->getId()
            )
        );
        $transactionSchema->setWallet($walletSchema);

        $transaction = $transactionRepository->create(
            $source->getId(),
            $transactionSchema,
            false
        );

        if (null === $transaction) {
            return null;
        }

        // perform amount addition on NU coin destination wallet
        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::NU_COIN_AGGREGATOR);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate($schema->getIssuedAt());
        $transactionSchema->setAmount($schema->getAmount());
        $transactionSchema->setType(TransactionType::INCOMING);
        $transactionSchema->setDescription(
            sprintf(
                '(nu-coin-aggregator::transfer-antar-organisasi): Kirim dana ' .
                'sejumlah %d ke aggregator wallet dibawah organisasi (nama: ' .
                '%s, id: %s) berhasil.',
                $schema->getAmount(),
                $destination->getName(),
                $destination->getId()
            )
        );
        $transactionSchema->setWallet($walletSchema);

        $transaction = $transactionRepository->create(
            $destination->getId(),
            $transactionSchema,
            false
        );

        if (null === $transaction) {
            return null;
        }

        $transferState = new NuCoinCrossTransferState();
        $transferState->setSource($source->getId());
        $transferState->setDestination($destination->getId());
        $transferState->setAmount($schema->getAmount());
        $transferState->setStatusCode(HttpCode::CREATED);

        return MapHydrator::create()->hydrate($transferState);
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param \Schnell\Entity\EntityInterface $source
     * @return bool|null
     */
    private function moveFundFromAggregatorToNuCoin(
        SchemaInterface $schema,
        EntityInterface $source
    ): bool|null {
        $wallet = $this->getWalletByOrganization($source, WalletType::NU_COIN_AGGREGATOR);

        if (null === $wallet) {
            throw new RepositoryException(
                $this->getRequest(),
                sprintf(
                    "NU coin aggregator wallet under organization ID: '%s' not found.",
                    $source->getId()
                )
            );
        }

        $walletAmount = $wallet->getAmount();
        $transactionRepository = new TransactionRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        if ($walletAmount > 0) {
            // perform amount reduction on NU coin aggregator wallet
            $walletSchema = new WalletSchema();
            $walletSchema->setType(WalletType::NU_COIN_AGGREGATOR);

            $transactionSchema = new TransactionSchema();
            $transactionSchema->setDate($schema->getIssuedAt());
            $transactionSchema->setAmount($walletAmount * -1);
            $transactionSchema->setType(TransactionType::OUTGOING);
            $transactionSchema->setDescription(
                // previous: transfer-antar-organisasi
                sprintf(
                    '(nu-coin-aggregator::pengosongan-wallet): Penarikan dana ' .
                    'sejumlah %d dari wallet dibawah organisasi (nama: ' .
                    '%s, id: %s) berhasil.',
                    $wallet->getAmount(),
                    $source->getName(),
                    $source->getId()
                )
            );
            $transactionSchema->setWallet($walletSchema);

            $transaction = $transactionRepository->create(
                $source->getId(),
                $transactionSchema,
                false
            );

            if (null === $transaction) {
                return null;
            }
        }

        // perform amount addition on NU coin destination wallet
        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::NU_COIN);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate($schema->getIssuedAt());
        $transactionSchema->setAmount($walletAmount);
        $transactionSchema->setType(TransactionType::INCOMING);
        $transactionSchema->setDescription(
            sprintf(
                '(nu-coin::terima-dana): Kirim dana ' .
                'sejumlah %d ke aggregator wallet dibawah organisasi (nama: ' .
                '%s, id: %s) berhasil.',
                $wallet->getAmount(),
                $source->getName(),
                $source->getId()
            )
        );
        $transactionSchema->setWallet($walletSchema);

        $transaction = $transactionRepository->create(
            $source->getId(),
            $transactionSchema,
            false
        );

        if (null === $transaction) {
            return null;
        }

        return true;
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\EntityInterface $parent
     * @return \Schnell\Entity\EntityInterface|null
     */
    private function getWalletByOrganization(
        EntityInterface $parent,
        int $type
    ): ?EntityInterface {
        $entity = new Wallet();
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $results = $queryBuilder
            ->select($entity->getQueryBuilderAlias())
            ->from($parent->getDqlName(), $parent->getQueryBuilderAlias())
            ->join(
                $entity->getDqlName(),
                $entity->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    "%s.id = %s.%s",
                    $parent->getQueryBuilderAlias(),
                    $entity->getQueryBuilderAlias(),
                    ltrim(rtrim($parent->getQueryBuilderAlias(), '_'), '_')
                )
            )
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq(
                    sprintf('%s.id', $parent->getQueryBuilderAlias()),
                    '?1'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.type', $entity->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, $parent->getId())
            ->setParameter(2, $type)
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
