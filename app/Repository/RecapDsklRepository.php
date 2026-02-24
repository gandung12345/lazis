<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use DateTimeImmutable;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Amil;
use Lazis\Api\Entity\Dskl;
use Lazis\Api\Entity\Donee;
use Lazis\Api\Entity\InfaqDistribution;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Organizer;
use Lazis\Api\Type\Dskl as DsklType;
use Lazis\Api\Type\Scope as ScopeType;
use Lazis\Api\Type\Wallet as WalletType;
use Lazis\Api\Type\ZakatDistribution as ZakatDistributionType;
use Schnell\Entity\EntityInterface;
use Schnell\Repository\AbstractRepository;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class RecapDsklRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @param string $oid
     * @param string $year
     * @return array
     */
    public function getRecap(string $oid, string $year): array
    {
        $organizationRepository = new OrganizationRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        $organization = $organizationRepository->getById($oid);

        if (null === $organization) {
            throw new RepositoryException(
                sprintf('Organization with id %s not found.', $oid)
            );
        }

        $organizationList = $this->aggregateChildOrganizationFromParent($organization);

        $result = [
            'year' => $year,
            'initialFund' => 0,
            'aggregation' => [
                'bpkh' => 0,
                'qurban' => 0,
                'fidyah' => 0
            ],
            'distribution' => [
                'nuCareSmart' => 0,
                'nuCareEmpowered' => 0,
                'nuCareHealthy' => 0,
                'nuCareGreen' => 0,
                'nuCarePeaceWithQurban' => 0,
                'nuCarePeaceWithoutQurban' => 0
            ],
            'dsklCalculatedFund' => 0
        ];

        $this->fetchInitialFund($year, $organizationList, $result);

        // aggregation
        $this->fetchBpkhAggregationFund($year, $organizationList, $result);
        $this->fetchQurbanAggregationFund($year, $organizationList, $result);
        $this->fetchFidyahAggregationFund($year, $organizationList, $result);

        // distribution
        $this->fetchDistributionFundForNuCareSmart($year, $organizationList, $result);
        $this->fetchDistributionFundForNuCareEmpowered($year, $organizationList, $result);
        $this->fetchDistributionFundForNuCareHealthy($year, $organizationList, $result);
        $this->fetchDistributionFundForNuCareGreen($year, $organizationList, $result);
        $this->fetchDistributionFundForNuCarePeaceWithQurban($year, $organizationList, $result);
        $this->fetchDistributionFundForNuCarePeaceWithoutQurban($year, $organizationList, $result);

        // calculated fund
        $this->fetchCalculatedFund($result);

        return $result;
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\EntityInterface $entity
     * @return array
     */
    private function aggregateChildOrganizationFromParent(EntityInterface $entity): array
    {
        if ($entity->getScope() === ScopeType::TWIG) {
            return [$entity];
        }

        if ($entity->getScope() === ScopeType::BRANCH_REPRESENTATIVE) {
            return $this->aggregateChildOrganizationFromBranchRepresentativeParent($entity);
        }

        return $this->aggregateChildOrganizationFromBranchParent($entity);
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\EntityInterface $entity
     * @return array
     */
    private function aggregateChildOrganizationFromBranchRepresentativeParent(EntityInterface $entity): array
    {
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder
            ->select($entity->getQueryBuilderAlias())
            ->from($entity->getDqlName(), $entity->getQueryBuilderAlias())
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq(
                    sprintf('%s.scope', $entity->getQueryBuilderAlias()),
                    '?1'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.district', $entity->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, ScopeType::TWIG)
            ->setParameter(2, $entity->getDistrict())
            ->getQuery()
            ->getResult();

        array_unshift($result, $entity);

        return $result;
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\EntityInterface $entity
     * @return array
     */
    private function aggregateChildOrganizationFromBranchParent(EntityInterface $entity): array
    {
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder
            ->select($entity->getQueryBuilderAlias())
            ->from($entity->getDqlName(), $entity->getQueryBuilderAlias())
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq(
                        sprintf('%s.scope', $entity->getQueryBuilderAlias()),
                        '?1'
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.scope', $entity->getQueryBuilderAlias()),
                        '?2'
                    )
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.district', $entity->getQueryBuilderAlias()),
                    '?3'
                )
            ))
            ->setParameter(1, ScopeType::BRANCH_REPRESENTATIVE)
            ->setParameter(2, ScopeType::TWIG)
            ->setParameter(3, $entity->getDistrict())
            ->getQuery()
            ->getResult();

        array_unshift($result, $entity);

        return $result;
    }

    /**
     * @internal
     *
     * @param string $year
     * @param array $organizationList
     * @param array &$result
     * @return void
     */
    private function fetchInitialFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Dskl()
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $dsklList = $queryBuilder
                ->select($entities[3]->getQueryBuilderAlias())
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
                ->join(
                    $entities[3]->getDqlName(),
                    $entities[3]->getQueryBuilderAlias(),
                    Expr\Join::WITH,
                    sprintf(
                        '%s.id = %s.amil',
                        $entities[2]->getQueryBuilderAlias(),
                        $entities[3]->getQueryBuilderAlias()
                    )
                )
                ->where($queryBuilder->expr()->eq(
                    sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                    '?1'
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?2 AND ?3', $entities[3]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, $startTz)
                ->setParameter(3, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($dsklList as $dskl) {
                $result['initialFund'] += abs($dskl->getAmount());
            }
        }

        return;
    }

    /**
     * @internal
     *
     * @param string $year
     * @param array $organizationList
     * @param array &$result
     * @return void
     */
    private function fetchBpkhAggregationFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Dskl()
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $dsklList = $queryBuilder
                ->select($entities[3]->getQueryBuilderAlias())
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
                ->join(
                    $entities[3]->getDqlName(),
                    $entities[3]->getQueryBuilderAlias(),
                    Expr\Join::WITH,
                    sprintf(
                        '%s.id = %s.amil',
                        $entities[2]->getQueryBuilderAlias(),
                        $entities[3]->getQueryBuilderAlias()
                    )
                )
                ->where($queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq(
                        sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                        '?1'
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.category', $entities[3]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[3]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, DsklType::BPKH)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($dsklList as $dskl) {
                $result['aggregation']['bpkh'] += abs($dskl->getAmount());
            }
        }

        return;
    }

    /**
     * @internal
     *
     * @param string $year
     * @param array $organizationList
     * @param array &$result
     * @return void
     */
    private function fetchQurbanAggregationFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Dskl()
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $dsklList = $queryBuilder
                ->select($entities[3]->getQueryBuilderAlias())
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
                ->join(
                    $entities[3]->getDqlName(),
                    $entities[3]->getQueryBuilderAlias(),
                    Expr\Join::WITH,
                    sprintf(
                        '%s.id = %s.amil',
                        $entities[2]->getQueryBuilderAlias(),
                        $entities[3]->getQueryBuilderAlias()
                    )
                )
                ->where($queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq(
                        sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                        '?1'
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.category', $entities[3]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[3]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, DsklType::QURBAN)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($dsklList as $dskl) {
                $result['aggregation']['qurban'] += abs($dskl->getAmount());
            }
        }

        return;
    }

    /**
     * @internal
     *
     * @param string $year
     * @param array $organizationList
     * @param array &$result
     * @return void
     */
    private function fetchFidyahAggregationFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Dskl()
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $dsklList = $queryBuilder
                ->select($entities[3]->getQueryBuilderAlias())
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
                ->join(
                    $entities[3]->getDqlName(),
                    $entities[3]->getQueryBuilderAlias(),
                    Expr\Join::WITH,
                    sprintf(
                        '%s.id = %s.amil',
                        $entities[2]->getQueryBuilderAlias(),
                        $entities[3]->getQueryBuilderAlias()
                    )
                )
                ->where($queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq(
                        sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                        '?1'
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.category', $entities[3]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[3]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, DsklType::FIDYAH)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($dsklList as $dskl) {
                $result['aggregation']['fidyah'] += abs($dskl->getAmount());
            }
        }

        return;
    }

    /**
     * @internal
     *
     * @param string $year
     * @param array $organizationList
     * @param array &$result
     * @return void
     */
    private function fetchDistributionFundForNuCareSmart(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new InfaqDistribution()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $infaqDistributionList = $queryBuilder
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
                        '%s.id = %s.donee',
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
                        sprintf('%s.program', $entities[2]->getQueryBuilderAlias()),
                        '?2'
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.fundingResource', $entities[2]->getQueryBuilderAlias()),
                        '?3'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?4 AND ?5', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, ZakatDistributionType::NU_CARE_SMART)
                ->setParameter(3, WalletType::ORGANIZATION_SOCIAL_FUNDING)
                ->setParameter(4, $startTz)
                ->setParameter(5, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqDistributionList as $infaqDistributionList) {
                $result['distribution']['nuCareSmart'] += abs($infaqDistribution->getAmount());
            }
        }

        return;
    }

    /**
     * @internal
     *
     * @param string $year
     * @param array $organizationList
     * @param array &$result
     * @return void
     */
    private function fetchDistributionFundForNuCareEmpowered(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new InfaqDistribution()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $infaqDistributionList = $queryBuilder
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
                        '%s.id = %s.donee',
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
                        sprintf('%s.program', $entities[2]->getQueryBuilderAlias()),
                        '?2'
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.fundingResource', $entities[2]->getQueryBuilderAlias()),
                        '?3'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?4 AND ?5', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, ZakatDistributionType::NU_CARE_EMPOWERED)
                ->setParameter(3, WalletType::ORGANIZATION_SOCIAL_FUNDING)
                ->setParameter(4, $startTz)
                ->setParameter(5, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqDistributionList as $infaqDistributionList) {
                $result['distribution']['nuCareEmpowered'] += abs($infaqDistribution->getAmount());
            }
        }

        return;
    }

    /**
     * @internal
     *
     * @param string $year
     * @param array $organizationList
     * @param array &$result
     * @return void
     */
    private function fetchDistributionFundForNuCareHealthy(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new InfaqDistribution()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $infaqDistributionList = $queryBuilder
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
                        '%s.id = %s.donee',
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
                        sprintf('%s.program', $entities[2]->getQueryBuilderAlias()),
                        '?2'
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.fundingResource', $entities[2]->getQueryBuilderAlias()),
                        '?3'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?4 AND ?5', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, ZakatDistributionType::NU_CARE_HEALTHY)
                ->setParameter(3, WalletType::ORGANIZATION_SOCIAL_FUNDING)
                ->setParameter(4, $startTz)
                ->setParameter(5, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqDistributionList as $infaqDistributionList) {
                $result['distribution']['nuCareHealthy'] += abs($infaqDistribution->getAmount());
            }
        }

        return;
    }

    /**
     * @internal
     *
     * @param string $year
     * @param array $organizationList
     * @param array &$result
     * @return void
     */
    private function fetchDistributionFundForNuCareGreen(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new InfaqDistribution()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $infaqDistributionList = $queryBuilder
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
                        '%s.id = %s.donee',
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
                        sprintf('%s.program', $entities[2]->getQueryBuilderAlias()),
                        '?2'
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.fundingResource', $entities[2]->getQueryBuilderAlias()),
                        '?3'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?4 AND ?5', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, ZakatDistributionType::NU_CARE_GREEN)
                ->setParameter(3, WalletType::ORGANIZATION_SOCIAL_FUNDING)
                ->setParameter(4, $startTz)
                ->setParameter(5, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqDistributionList as $infaqDistributionList) {
                $result['distribution']['nuCareGreen'] += abs($infaqDistribution->getAmount());
            }
        }

        return;
    }

    /**
     * @internal
     *
     * @param string $year
     * @param array $organizationList
     * @param array &$result
     * @return void
     */
    private function fetchDistributionFundForNuCarePeaceWithQurban(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new InfaqDistribution()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $infaqDistributionList = $queryBuilder
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
                        '%s.id = %s.donee',
                        $entities[1]->getQueryBuilderAlias(),
                        $entities[2]->getQueryBuilderAlias()
                    )
                )
                ->where($queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq(
                        sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                        '?1'
                    ),
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq(
                            sprintf('%s.program', $entities[2]->getQueryBuilderAlias()),
                            '?2'
                        ),
                        $queryBuilder->expr()->eq(
                            sprintf('%s.program', $entities[2]->getQueryBuilderAlias()),
                            '?3'
                        )
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.fundingResource', $entities[2]->getQueryBuilderAlias()),
                        '?4'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?5 AND ?6', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, ZakatDistributionType::NU_CARE_PEACE)
                ->setParameter(3, ZakatDistributionType::NU_CARE_QURBAN)
                ->setParameter(4, WalletType::ORGANIZATION_SOCIAL_FUNDING)
                ->setParameter(5, $startTz)
                ->setParameter(6, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqDistributionList as $infaqDistributionList) {
                $result['distribution']['nuCarePeaceWithQurban'] += abs($infaqDistribution->getAmount());
            }
        }

        return;
    }

    /**
     * @internal
     *
     * @param string $year
     * @param array $organizationList
     * @param array &$result
     * @return void
     */
    private function fetchDistributionFundForNuCarePeaceWithoutQurban(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new InfaqDistribution()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $infaqDistributionList = $queryBuilder
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
                        '%s.id = %s.donee',
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
                        sprintf('%s.program', $entities[2]->getQueryBuilderAlias()),
                        '?2'
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.fundingResource', $entities[2]->getQueryBuilderAlias()),
                        '?3'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?4 AND ?5', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, ZakatDistributionType::NU_CARE_PEACE)
                ->setParameter(3, WalletType::ORGANIZATION_SOCIAL_FUNDING)
                ->setParameter(4, $startTz)
                ->setParameter(5, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqDistributionList as $infaqDistributionList) {
                $result['distribution']['nuCarePeaceWithoutQurban'] += abs($infaqDistribution->getAmount());
            }
        }

        return;
    }

    /**
     * @internal
     *
     * @param array &$result
     * @return void
     */
    private function fetchCalculatedFund(array &$result): void
    {
        $initialFund = $result['initialFund'];
        $distributionFund = array_sum(array_values($result['distribution']));

        $result['dsklCalculatedFund'] = $initialFund - $distributionFund;
        return;
    }
}
