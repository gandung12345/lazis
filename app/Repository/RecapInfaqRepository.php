<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use DateTimeImmutable;
use Lazis\Api\Entity\Amil;
use Lazis\Api\Entity\Donee;
use Lazis\Api\Entity\Infaq;
use Lazis\Api\Entity\InfaqDistribution;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Organizer;
use Lazis\Api\Entity\Transaction;
use Lazis\Api\Entity\Wallet;
use Lazis\Api\Repository\OrganizationRepository;
use Lazis\Api\Type\InfaqProgram as InfaqProgramType;
use Lazis\Api\Type\Scope as ScopeType;
use Lazis\Api\Type\ZakatDistribution as ZakatDistributionType;
use Schnell\Entity\EntityInterface;
use Schnell\Repository\AbstractRepository;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class RecapInfaqRepository extends AbstractRepository
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
                'bounded' => 0,
                'unbounded' => 0,
                'campaignProgram' => 0,
                'donation' => 0
            ],
            'distribution' => [
                'nuCareSmart' => 0,
                'nuCareEmpowered' => 0,
                'nuCareHealthy' => 0,
                'nuCareGreen' => 0,
                'nuCarePeace' => 0,
                'amilFund' => 0
            ],
            'calculatedInfaqFund' => 0
        ];

        $this->fetchInitialFund($year, $organizationList, $result);

        // aggregation
        $this->fetchBoundedFund($year, $organizationList, $result);
        $this->fetchUnboundedFund($year, $organizationList, $result);
        $this->fetchCampaignProgramFund($year, $organizationList, $result);
        $this->fetchDonationFund($year, $organizationList, $result);

        // distribution
        $this->fetchDistributionForNuCareSmart($year, $organizationList, $result);
        $this->fetchDistributionForNuCareEmpowered($year, $organizationList, $result);
        $this->fetchDistributionForNuCareHealthy($year, $organizationList, $result);
        $this->fetchDistributionForNuCareGreen($year, $organizationList, $result);
        $this->fetchDistributionForNuCarePeace($year, $organizationList, $result);
        $this->fetchDistributionForAmil($year, $organizationList, $result);

        // final calculation
        $this->calculateInfaqFund($result);

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
                    sprintf('%s.scope = ?1', $entity->getQueryBuilderAlias())
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.district = ?2', $entity->getQueryBuilderAlias())
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
                        sprintf('%s.scope = ?1', $entity->getQueryBuilderAlias())
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.scope = ?2', $entity->getQueryBuilderAlias())
                    )
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.district = ?3', $entity->getQueryBuilderAlias())
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
            new Amil(), new Infaq()
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $infaqList = $queryBuilder
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

            foreach ($infaqList as $infaq) {
                $result['initialFund'] += abs($infaq->getAmount());
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
    private function fetchBoundedFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Infaq()
        ];

        $boundedConstraints = [
            InfaqProgramType::NU_CARE_SMART,
            InfaqProgramType::NU_CARE_EMPOWERED,
            InfaqProgramType::NU_CARE_HEALTHY,
            InfaqProgramType::NU_CARE_GREEN,
            InfaqProgramType::NU_CARE_PEACE
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $infaqList = $queryBuilder
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
                ->andWhere(sprintf('%s.program IN (?2)', $entities[3]->getQueryBuilderAlias()))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[3]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, $boundedConstraints)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqList as $infaq) {
                $result['aggregation']['bounded'] += abs($infaq->getAmount());
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
    private function fetchUnboundedFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Infaq()
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $infaqList = $queryBuilder
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
                        sprintf('%s.program', $entities[3]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[3]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, InfaqProgramType::UNBOUNDED)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqList as $infaq) {
                $result['aggregation']['unbounded'] += abs($infaq->getAmount());
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
    private function fetchCampaignProgramFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Infaq()
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $infaqList = $queryBuilder
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
                        sprintf('%s.program', $entities[3]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[3]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, InfaqProgramType::CAMPAIGN_PROGRAM)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqList as $infaq) {
                $result['aggregation']['campaignProgram'] += abs($infaq->getAmount());
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
    private function fetchDonationFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Infaq()
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $infaqList = $queryBuilder
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
                        sprintf('%s.program', $entities[3]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[3]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, InfaqProgramType::DONATION)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqList as $infaq) {
                $result['aggregation']['donation'] += abs($infaq->getAmount());
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
    private function fetchDistributionForNuCareSmart(
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
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, ZakatDistributionType::NU_CARE_SMART)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqDistributionList as $infaqDistribution) {
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
    private function fetchDistributionForNuCareEmpowered(
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
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, ZakatDistributionType::NU_CARE_EMPOWERED)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqDistributionList as $infaqDistribution) {
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
    private function fetchDistributionForNuCareHealthy(
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
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, ZakatDistributionType::NU_CARE_HEALTHY)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqDistributionList as $infaqDistribution) {
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
    private function fetchDistributionForNuCareGreen(
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
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, ZakatDistributionType::NU_CARE_GREEN)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqDistributionList as $infaqDistribution) {
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
    private function fetchDistributionForNuCarePeace(
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
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, ZakatDistributionType::NU_CARE_PEACE)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqDistributionList as $infaqDistribution) {
                $result['distribution']['nuCarePeace'] += abs($infaqDistribution->getAmount());
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
    private function fetchDistributionForAmil(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Wallet(), new Transaction()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

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
                        '%s.id = %s.wallet',
                        $entities[1]->getQueryBuilderAlias(),
                        $entities[2]->getQueryBuilderAlias()
                    )
                )
                ->where($queryBuilder->expr()->eq(
                    sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                    '?1'
                ))
                ->andWhere(sprintf('%s.description LIKE ?2', $entities[2]->getQueryBuilderAlias()))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, '(infaq::amil-funding-cut)%')
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($infaqDistributionList as $infaqDistribution) {
                $result['distribution']['amilFund'] += abs($infaqDistribution->getAmount());
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
    private function calculateInfaqFund(array &$result): void
    {
        $initialFund = $result['initialFund'];
        $distributedFund = array_sum(array_values($result['distribution']));

        $result['calculatedInfaqFund'] = $initialFund - $distributedFund;
        return;
    }
}
