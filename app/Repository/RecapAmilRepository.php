<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use DateTimeImmutable;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Amil;
use Lazis\Api\Entity\AmilFunding;
use Lazis\Api\Entity\AmilFundingUsage;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Organizer;
use Lazis\Api\Entity\Transaction;
use Lazis\Api\Entity\Wallet;
use Lazis\Api\Repository\Exception\RepositoryException;
use Lazis\Api\Type\AmilFunding as AmilFundingType;
use Lazis\Api\Type\AmilFundingDistribution as AmilFundingDistributionType;
use Lazis\Api\Type\Scope as ScopeType;
use Schnell\Entity\EntityInterface;
use Schnell\Repository\AbstractRepository;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class RecapAmilRepository extends AbstractRepository
{
    use RepositoryTrait;

    public function getRecap(string $oid, string $year): array
    {
        $organizationRepository = new OrganizationRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        $organization = $organizationRepository->getById($oid);

        if (null === $organization) {
            throw new RepositoryException(
                $this->getRequest(),
                sprintf('Organization with id %s not found.', $oid)
            );
        }

        $organizationList = $this->aggregateChildOrganizationFromParent($organization);

        $result = [
            'year' => $year,
            'initialFund' => 0,
            'aggregation' => [
                'zakatAllocation' => 0,
                'infaqAllocation' => 0,
                'dsklAllocation' => 0,
                'other' => 0,
                'grant' => 0
            ],
            'distribution' => [
                'socializationAndEducation' => 0,
                'employeeExpense' => 0,
                'officeUtilitiesFund' => 0,
                'officeStationeryFund' => 0,
                'internetFund' => 0,
                'telephoneFund' => 0,
                'electricityFund' => 0,
                'transportationFund' => 0,
                'communicationFund' => 0,
                'officeUtilitiesMaintenanceFund' => 0,
                'consumptionFund' => 0,
                'insuranceFund' => 0,
                'generalAndAdministrationFund' => 0,
                'depreciationExpense' => 0
            ],
            'calculatedAmilFund' => 0
        ];

        // initial fund
        $this->fetchInitialFund($year, $organizationList, $result);

        // distribution
        $this->fetchDistributionForSocializationAndEducation($year, $organizationList, $result);
        $this->fetchDistributionForEmployeeExpense($year, $organizationList, $result);
        $this->fetchDistributionForOfficeUtilitiesFund($year, $organizationList, $result);
        $this->fetchDistributionForOfficeStationeryFund($year, $organizationList, $result);
        $this->fetchDistributionForInternetFund($year, $organizationList, $result);
        $this->fetchDistributionForTelephoneFund($year, $organizationList, $result);
        $this->fetchDistributionForElectricityFund($year, $organizationList, $result);
        $this->fetchDistributionForTransportationFund($year, $organizationList, $result);
        $this->fetchDistributionForCommunicationFund($year, $organizationList, $result);
        $this->fetchDistributionForOfficeUtilitiesMaintenanceFund($year, $organizationList, $result);
        $this->fetchDistributionForConsumptionFund($year, $organizationList, $result);
        $this->fetchDistributionForInsuranceFund($year, $organizationList, $result);
        $this->fetchDistributionForGeneralAndAdministrationFund($year, $organizationList, $result);
        $this->fetchDistributionForDepreciationExpense($year, $organizationList, $result);

        // amil final calculated fund
        $this->calculateFinalAmilFund($result);

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
            ->where($queryBuilder->expr->andX(
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
        $this->fetchAggregationFromZakatAllocation($year, $organizationList, $result);
        $this->fetchAggregationFromInfaqAllocation($year, $organizationList, $result);
        $this->fetchAggregationFromDsklAllocation($year, $organizationList, $result);
        $this->fetchAggregationFromOtherAllocation($year, $organizationList, $result);
        $this->fetchAggregationFromGrantAllocation($year, $organizationList, $result);

        $result['initialFund'] = abs(array_sum(array_values($result['aggregation'])));
    }

    /**
     * @internal
     *
     * @param string $year
     * @param array $organizationList
     * @param array &$result
     * @return void
     */
    private function fetchAggregationFromZakatAllocation(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Wallet(), new Transaction()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingList = $queryBuilder
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
                ->setParameter(2, '(zakat::amil-funding-cut)%')
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingList as $amilFunding) {
                $result['aggregation']['zakatAllocation'] += abs($amilFunding->getAmount());
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
    private function fetchAggregationFromInfaqAllocation(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Wallet(), new Transaction()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingList = $queryBuilder
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

            foreach ($amilFundingList as $amilFunding) {
                $result['aggregation']['infaqAllocation'] += abs($amilFunding->getAmount());
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
    private function fetchAggregationFromDsklAllocation(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Wallet(), new Transaction()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingList = $queryBuilder
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
                ->setParameter(2, '(dskl::amil-funding-cut)%')
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingList as $amilFunding) {
                $result['aggregation']['dsklAllocation'] += abs($amilFunding->getAmount());
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
    private function fetchAggregationFromOtherAllocation(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new AmilFunding()
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingList = $queryBuilder
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
                        sprintf('%s.fundingType', $entities[3]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[3]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingType::OTHER_AMIL)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingList as $amilFunding) {
                $result['aggregation']['other'] += abs($amilFunding->getAmount());
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
    private function fetchAggregationFromGrantAllocation(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new AmilFunding()
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingList = $queryBuilder
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
                        sprintf('%s.fundingType', $entities[3]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[3]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingType::GRANT_FUNDS)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingList as $amilFunding) {
                $result['aggregation']['grant'] += abs($amilFunding->getAmount());
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
    private function fetchDistributionForSocializationAndEducation(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::SOCIAL_AND_EDUCATION_FUNDING)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['socializationAndEducation'] += abs($amilFundingDistribution->getAmount());
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
    private function fetchDistributionForEmployeeExpense(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::EMPLOYEE_EXPENSES)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['employeeExpense'] += abs($amilFundingDistribution->getAmount());
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
    private function fetchDistributionForOfficeUtilitiesFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::OFFICE_EQUIPMENT_COST)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['officeUtilitiesFund'] += abs($amilFundingDistribution->getAmount());
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
    private function fetchDistributionForOfficeStationeryFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::OFFICE_STATIONERY_COST)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['officeStationeryFund'] += abs($amilFundingDistribution->getAmount());
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
    private function fetchDistributionForInternetFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::INTERNET_COST)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['internetFund'] += abs($amilFundingDistribution->getAmount());
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
    private function fetchDistributionForTelephoneFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::PHONE_BILL_COST)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['telephoneFund'] += abs($amilFundingDistribution->getAmount());
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
    private function fetchDistributionForElectricityFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::ELECTRIC_BILL_COST)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['electricityFund'] += abs($amilFundingDistribution->getAmount());
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
    private function fetchDistributionForTransportationFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::TRANSPORTATION_COST)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['transportationFund'] += abs($amilFundingDistribution->getAmount());
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
    private function fetchDistributionForCommunicationFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::COMMUNICATION_COST)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['communicationFund'] += abs($amilFundingDistribution->getAmount());
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
    private function fetchDistributionForOfficeUtilitiesMaintenanceFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::OFFICE_ASSET_MAINTENANCE_COST)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['officeUtilitiesMaintenanceFund'] += abs($amilFundingDistribution->getAmount());
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
    private function fetchDistributionForConsumptionFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::FOOD_AND_BEVERAGE_COST)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['consumptionFund'] += abs($amilFundingDistribution->getAmount());
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
    private function fetchDistributionForInsuranceFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::INSURANCE_COST)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['insuranceFund'] += abs($amilFundingDistribution->getAmount());
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
    private function fetchDistributionForGeneralAndAdministrationFund(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::ADMIN_AND_COMMON_COST)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['generalAndAdministrationFund'] += abs($amilFundingDistribution->getAmount());
            }
        }

        return;
    }

    /**
     * @internal
     *
     * @param string $year
     * @param array $organizationList
     * @param &$result
     * @return void
     */
    private function fetchDistributionForDepreciationExpense(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $amilFundingDistributionList = $queryBuilder
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
                        sprintf('%s.usageType', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AmilFundingDistributionType::DEPRECIATION_EXPENSE)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($amilFundingDistributionList as $amilFundingDistribution) {
                $result['distribution']['depreciationExpense'] += abs($amilFundingDistribution->getAmount());
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
    private function calculateFinalAmilFund(array &$result): void
    {
        $aggregation = array_sum(array_values($result['aggregation']));
        $distribution = array_sum(array_values($result['distribution']));

        $result['calculatedAmilFund'] = $aggregation - $distribution;
    }
}
