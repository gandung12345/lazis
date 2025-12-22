<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use DateTime;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Amil;
use Lazis\Api\Entity\Dashboard;
use Lazis\Api\Entity\Donee;
use Lazis\Api\Entity\Donor;
use Lazis\Api\Entity\NuCoinStatistics;
use Lazis\Api\Entity\NuCoinAggregatorStatistics;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Organizer;
use Lazis\Api\Entity\Transaction;
use Lazis\Api\Entity\Volunteer;
use Lazis\Api\Entity\Wallet;
use Lazis\Api\Type\Transaction as TransactionType;
use Lazis\Api\Type\Wallet as WalletType;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\EntityInterface;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;

use function array_map;
use function array_sum;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DashboardRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @param \Schnell\Entity\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface
     */
    public function getStatistics(SchemaInterface $schema): EntityInterface
    {
        $dashboard = new Dashboard();
        $dashboard->setDonorCount($this->getDonorCount($schema));
        $dashboard->setDoneeCount($this->getDoneeCount($schema));
        $dashboard->setVolunteerCount($this->getVolunteerCount($schema));
        $dashboard->setAmilCount($this->getAmilCount($schema));
        $dashboard->setOrganizerCount($this->getOrganizerCount($schema));
        $dashboard->setAllWalletFunds($this->getAllWalletFunds($schema));

        $this->getNuCoinStatistics($schema, $dashboard);

        return $dashboard;
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\SchemaInterface $schema
     * @return int
     */
    private function getDonorCount(SchemaInterface $schema): int
    {
        $entities = [new Organization(), new Volunteer(), new Donor()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder
            ->select(
                sprintf(
                    'count(%s)',
                    $entities[2]->getQueryBuilderAlias()
                )
            )
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
            ->join(
                $entities[2]->getDqlName(),
                $entities[2]->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.volunteer',
                    $entities[1]->getQueryBuilderAlias(),
                    $entities[2]->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\SchemaInterface $schema
     * @return int
     */
    private function getDoneeCount(SchemaInterface $schema): int
    {
        $entities = [new Organization(), new Donee()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder
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
                    '%s.id = %s.organization',
                    $entities[0]->getQueryBuilderAlias(),
                    $entities[1]->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\SchemaInterface $schema
     * @return int
     */
    private function getVolunteerCount(SchemaInterface $schema): int
    {
        $entities = [new Organization(), new Volunteer()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder
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
                    '%s.id = %s.organization',
                    $entities[0]->getQueryBuilderAlias(),
                    $entities[1]->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\SchemaInterface $schema
     * @return int
     */
    private function getAmilCount(SchemaInterface $schema): int
    {
        $entities = [new Organization(), new Organizer(), new Amil()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder
            ->select(
                sprintf(
                    'count(%s)',
                    $entities[2]->getQueryBuilderAlias()
                )
            )
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
            ->join(
                $entities[2]->getDqlName(),
                $entities[2]->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.organizer',
                    $entities[1]->getQueryBuilderAlias(),
                    $entities[2]->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\SchemaInterface $schema
     * @return int
     */
    private function getOrganizerCount(SchemaInterface $schema): int
    {
        $entities = [new Organization(), new Organizer()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder
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
                    '%s.id = %s.organization',
                    $entities[0]->getQueryBuilderAlias(),
                    $entities[1]->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\SchemaInterface $schema
     * @return int
     */
    private function getAllWalletFunds(SchemaInterface $schema): int
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
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->getQuery()
            ->getResult();

        return array_sum(array_map(function ($wallet) {
            return $wallet->getAmount();
        }, $result));
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param \Schnell\Entity\EntityInterface &$entity
     * @return void
     */
    private function getNuCoinStatistics(
        SchemaInterface $schema,
        EntityInterface &$entity
    ): void {
        $dateTime = new DateTime();
        $currentYear = intval($dateTime->format('Y'));
        $aggregation = [];
        $distribution = [];

        for ($i = 1; $i <= 12; $i++) {
            $agg = $this->findNuCoinStatistics(
                $schema,
                $currentYear,
                $i,
                WalletType::NU_COIN_AGGREGATOR
            );

            $distrib = $this->findNuCoinStatistics(
                $schema,
                $currentYear,
                $i,
                WalletType::NU_COIN
            );

            $aggregation[] = $agg;
            $distribution[] = $distrib;
        }

        $entity->setNuCoinYearlyStatistics($distribution);
        $entity->setNuCoinAggregatorYearlyStatistics($aggregation);
        return;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param int $year
     * @param int $month
     * @param int $walletType
     * @return \Schnell\Entity\EntityInterface
     */
    private function findNuCoinStatistics(
        SchemaInterface $schema,
        int $year,
        int $month,
        int $walletType
    ): EntityInterface {
        $dateTemplate = sprintf(
            '%d-%s',
            $year,
            $month >= 1 && $month <= 9 ? '0' . strval($month) : strval($month)
        );
        $entities = [new Organization(), new Wallet(), new Transaction()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $results = $queryBuilder
            ->select($entities[2]->getQueryBuilderAlias())
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
            ->join(
                $entities[2]->getDqlName(),
                $entities[2]->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.wallet',
                    $entities[1]->getQueryBuilderAlias(),
                    $entities[2]->getQueryBuilderAlias()
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
                ),
                $queryBuilder->expr()->between(
                    sprintf('%s.date', $entities[2]->getQueryBuilderAlias()),
                    '?3',
                    '?4'
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, $walletType)
            ->setParameter(
                3,
                DateTimeDecorator::createFromFormat('Y-m-d', $dateTemplate . '-01')
            )
            ->setParameter(
                4,
                DateTimeDecorator::createFromFormat('Y-m-d', $dateTemplate . '-31')
            )
            ->getQuery()
            ->getResult();

        $entity = $walletType === WalletType::NU_COIN
            ? new NuCoinStatistics()
            : new NuCoinAggregatorStatistics();

        $entity->setDate($dateTemplate);

        foreach ($results as $result) {
            if ($result->getType() === TransactionType::INCOMING) {
                $entity->incrementIncomingTransactionCount();
            }

            if ($result->getType() === TransactionType::OUTGOING) {
                $entity->incrementOutgoingTransactionCount();
            }
        }

        return $entity;
    }
}
