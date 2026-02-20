<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use DateTimeImmutable;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Amil;
use Lazis\Api\Entity\Donee;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Organizer;
use Lazis\Api\Entity\Zakat;
use Lazis\Api\Entity\ZakatDistribution;
use Lazis\Api\Repository\Exception\RepositoryException;
use Lazis\Api\Type\Asnaf as AsnafType;
use Lazis\Api\Type\Muzakki as MuzakkiType;
use Lazis\Api\Type\Scope as ScopeType;
use Lazis\Api\Type\Zakat as ZakatType;
use Lazis\Api\Type\ZakatDistribution as ZakatDistributionType;
use Schnell\Entity\EntityInterface;
use Schnell\Repository\AbstractRepository;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class RecapZakatRepository extends AbstractRepository
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
            'zakatAggregation' => [
                'zakatMaalFundingPersonal' => 0,
                'zakatMaalFundingCollective' => 0,
                'zakatFitrFunding' => 0
            ],
            'asnafBasedZakatDistribution' => [
                'fakir' => 0,
                'poor' => 0,
                'fisabilillah' => 0,
                'ibnSabil' => 0,
                'gharim' => 0,
                'mualaf' => 0,
                'amilCut' => 0
            ],
            'programBasedDistribution' => [
                'nuCareSmart' => 0,
                'nuCareEmpowered' => 0,
                'nuCareHealthy' => 0,
                'nuCareGreen' => 0,
                'nuCarePeace' => 0
            ],
            'zakatCalculatedFund' => 0
        ];

        // zakat aggregation
        $this->fetchZakatAggregationForMaalFundingPersonal($year, $organizationList, $result);
        $this->fetchZakatAggregationForMaalFundingCollective($year, $organizationList, $result);
        $this->fetchZakatAggregationForFitrFunding($year, $organizationList, $result);

        // asnaf based zakat distribution
        $this->fetchAsnafBasedZakatDistributionForFakir($year, $organizationList, $result);
        $this->fetchAsnafbasedZakatDistributionForPoor($year, $organizationList, $result);
        $this->fetchAsnafBasedZakatDistributionForFisabilillah($year, $organizationList, $result);
        $this->fetchAsnafBasedZakatDistributionForIbnSabil($year, $organizationList, $result);
        $this->fetchAsnafBasedZakatDistributionForGharim($year, $organizationList, $result);
        $this->fetchAsnafBasedZakatDistributionForMualaf($year, $organizationList, $result);
        $this->fetchAsnafBasedZakatDistributionForAmilCut($year, $organizationList, $result);

        // program based distribution
        $this->fetchProgramBasedZakatDistributionForNuCareSmart($year, $organizationList, $result);
        $this->fetchProgramBasedZakatDistributionForNuCareEmpowered($year, $organizationList, $result);
        $this->fetchProgramBasedZakatDistributionForNuCareHealthy($year, $organizationList, $result);
        $this->fetchProgramBasedZakatDistributionForNuCareGreen($year, $organizationList, $result);
        $this->fetchProgramBasedZakatDistributionForNuCarePeace($year, $organizationList, $result);

        // calculate total zakat fund
        $this->calculateTotalZakatFunding($result);
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
     * @param array $organizationList
     * @param array &$result
     * @return void
     */
    private function fetchZakatAggregationForMaalFundingPersonal(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Zakat()
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $zakatList = $queryBuilder
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
                        sprintf('%s.muzakki', $entities[3]->getQueryBuilderAlias()),
                        '?2'
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.type', $entities[3]->getQueryBuilderAlias()),
                        '?3'
                    )
                ))
                ->andWhere(sprintf(
                    '%s.date BETWEEN ?4 AND ?5',
                    $entities[3]->getQueryBuilderAlias()
                ))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, MuzakkiType::PERSONAL)
                ->setParameter(3, ZakatType::MAAL)
                ->setParameter(4, $startTz)
                ->setParameter(5, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($zakatList as $zakatObj) {
                $result['zakatAggregation']['zakatMaalFundingPersonal'] += $zakatObj->getAmount();
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
    private function fetchZakatAggregationForMaalFundingCollective(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Zakat()
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $zakatList = $queryBuilder
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
                        sprintf('%s.muzakki', $entities[3]->getQueryBuilderAlias()),
                        '?2'
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.type', $entities[3]->getQueryBuilderAlias()),
                        '?3'
                    )
                ))
                ->andWhere(sprintf(
                    '%s.date BETWEEN ?4 AND ?5',
                    $entities[3]->getQueryBuilderAlias()
                ))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, MuzakkiType::COLLECTIVE)
                ->setParameter(3, ZakatType::MAAL)
                ->setParameter(4, $startTz)
                ->setParameter(5, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($zakatList as $zakatObj) {
                $result['zakatAggregation']['zakatMaalFundingCollective'] += $zakatObj->getAmount();
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
    private function fetchZakatAggregationForFitrFunding(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Zakat()
        ];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $zakatList = $queryBuilder
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
                        sprintf('%s.type', $entities[3]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf(
                    '%s.date BETWEEN ?3 AND ?4',
                    $entities[3]->getQueryBuilderAlias()
                ))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, ZakatType::FITRAH)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($zakatList as $zakatObj) {
                $result['zakatAggregation']['zakatFitrFunding'] += $zakatObj->getAmount();
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
    private function fetchAsnafBasedZakatDistributionForFakir(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        foreach ($organizationList as $organization) {
            $zakatDistributionList = $queryBuilder
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
                        sprintf('%s.asnaf', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AsnafType::FAKIR)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($zakatDistributionList as $zakatDistribution) {
                $result['asnafBasedZakatDistribution']['fakir'] += $zakatDistribution->getAmount();
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
    private function fetchAsnafbasedZakatDistributionForPoor(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        foreach ($organizationList as $organization) {
            $zakatDistributionList = $queryBuilder
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
                        sprintf('%s.asnaf', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AsnafType::POOR)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($zakatDistributionList as $zakatDistribution) {
                $result['asnafBasedZakatDistribution']['poor'] += $zakatDistribution->getAmount();
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
    private function fetchAsnafBasedZakatDistributionForFisabilillah(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        foreach ($organizationList as $organization) {
            $zakatDistributionList = $queryBuilder
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
                        sprintf('%s.asnaf', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AsnafType::FISABILILLAH)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($zakatDistributionList as $zakatDistribution) {
                $result['asnafBasedZakatDistribution']['fisabilillah'] += $zakatDistribution->getAmount();
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
    private function fetchAsnafBasedZakatDistributionForIbnSabil(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        foreach ($organizationList as $organization) {
            $zakatDistributionList = $queryBuilder
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
                        sprintf('%s.asnaf', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AsnafType::IBNU_SABIL)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($zakatDistributionList as $zakatDistribution) {
                $result['asnafBasedZakatDistribution']['ibnSabil'] += $zakatDistribution->getAmount();
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
    private function fetchAsnafBasedZakatDistributionForGharim(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        foreach ($organizationList as $organization) {
            $zakatDistributionList = $queryBuilder
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
                        sprintf('%s.asnaf', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AsnafType::GHARIM)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($zakatDistributionList as $zakatDistribution) {
                $result['asnafBasedZakatDistribution']['gharim'] += $zakatDistribution->getAmount();
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
    private function fetchAsnafBasedZakatDistributionForMualaf(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        foreach ($organizationList as $organization) {
            $zakatDistributionList = $queryBuilder
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
                        sprintf('%s.asnaf', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AsnafType::MUALAF)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($zakatDistributionList as $zakatDistribution) {
                $result['asnafBasedZakatDistribution']['mualaf'] += $zakatDistribution->getAmount();
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
    private function fetchAsnafBasedZakatDistributionForAmilCut(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        foreach ($organizationList as $organization) {
            $zakatDistributionList = $queryBuilder
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
                        sprintf('%s.asnaf', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, AsnafType::AMIL)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($zakatDistributionList as $zakatDistribution) {
                $result['asnafBasedZakatDistribution']['amilCut'] += $zakatDistribution->getAmount();
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
    private function fetchProgramBasedZakatDistributionForNuCareSmart(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        foreach ($organizationList as $organization) {
            $zakatDistributionList = $queryBuilder
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

            foreach ($zakatDistributionList as $zakatDistribution) {
                $result['programBasedDistribution']['nuCareSmart'] += $zakatDistribution->getAmount();
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
    private function fetchProgramBasedZakatDistributionForNuCareEmpowered(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        foreach ($organizationList as $organization) {
            $zakatDistributionList = $queryBuilder
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

            foreach ($zakatDistributionList as $zakatDistribution) {
                $result['programBasedDistribution']['nuCareEmpowered'] += $zakatDistribution->getAmount();
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
    private function fetchProgramBasedZakatDistributionForNuCareHealthy(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        foreach ($organizationList as $organization) {
            $zakatDistributionList = $queryBuilder
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

            foreach ($zakatDistributionList as $zakatDistribution) {
                $result['programBasedDistribution']['nuCareHealthy'] += $zakatDistribution->getAmount();
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
    private function fetchProgramBasedZakatDistributionForNuCareGreen(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        foreach ($organizationList as $organization) {
            $zakatDistributionList = $queryBuilder
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

            foreach ($zakatDistributionList as $zakatDistribution) {
                $result['programBasedDistribution']['nuCareGreen'] += $zakatDistribution->getAmount();
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
    private function fetchProgramBasedZakatDistributionForNuCarePeace(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        foreach ($organizationList as $organization) {
            $zakatDistributionList = $queryBuilder
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

            foreach ($zakatDistributionList as $zakatDistribution) {
                $result['programBasedDistribution']['nuCarePeace'] += $zakatDistribution->getAmount();
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
    private function calculateTotalZakatFunding(array &$result): void
    {
        $aggregation = array_sum(array_values($result['zakatAggregation']));
        $asnafBasedDistribution = array_sum(array_values($result['asnafBasedZakatDistribution']));
        $programBasedDistribution = array_sum(array_values($result['programBasedDistribution']));

        $divisor = intval($asnafBasedDistribution + $programBasedDistribution);

        if ($divisor === 0) {
            $result['zakatCalculatedFund'] = 0;
            return;
        }

        $result['zakatCalculatedFund'] = intval($aggregation / $divisor);
        return;
    }
}
