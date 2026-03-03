<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use DateTimeImmutable;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\NonHalalFundingDistribution;
use Lazis\Api\Entity\NonHalalFundingReceive;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Repository\Exception\RepositoryException;
use Lazis\Api\Type\NonHalalDistribution as NonHalalDistributionType;
use Lazis\Api\Type\NonHalalFunding as NonHalalFundingType;
use Lazis\Api\Type\Scope as ScopeType;
use Schnell\Entity\EntityInterface;
use Schnell\Repository\AbstractRepository;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class RecapNonHalalFundingRepository extends AbstractRepository
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
                $this->getRequest(),
                sprintf('Organization with id %s not found.', $oid)
            );
        }

        $organizationList = $this->aggregateChildOrganizationFromParent($organization);

        $result = [
            'year' => $year,
            'aggregation' => [
                'bankInterest' => 0,
                'currentAccountService' => 0,
                'other' => 0
            ],
            'distribution' => [
                'bankAdmin' => 0,
                'nonHalalFundingUsage' => 0
            ]
        ];

        // aggregation
        $this->fetchAggregationFromBankInterest($year, $organizationList, $result);
        $this->fetchAggregationFromCurrentAccountService($year, $organizationList, $result);
        $this->fetchAggregationFromOther($year, $organizationList, $result);

        // distribution
        $this->fetchDistributionForBankAdmin($year, $organizationList, $result);
        $this->fetchDistributionForNonHalalFundingUsage($year, $organizationList, $result);

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
    private function fetchAggregationFromBankInterest(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new NonHalalFundingReceive()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $nonHalalAggregationList = $queryBuilder
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
                        sprintf('%s.kind', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, NonHalalFundingType::BANK_INTEREST)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($nonHalalAggregationList as $nonHalalAggregation) {
                $result['aggregation']['bankInterest'] += abs($nonHalalAggregation->getAmount());
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
    private function fetchAggregationFromCurrentAccountService(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new NonHalalFundingReceive()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $nonHalalAggregationList = $queryBuilder
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
                        sprintf('%s.kind', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, NonHalalFundingType::CURRENT_ACCOUNT_SERVICE)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($nonHalalAggregationList as $nonHalalAggregation) {
                $result['aggregation']['currentAccountService'] += abs($nonHalalAggregation->getAmount());
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
    private function fetchAggregationFromOther(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new NonHalalFundingReceive()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $nonHalalAggregationList = $queryBuilder
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
                        sprintf('%s.kind', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, NonHalalFundingType::OTHER)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($nonHalalAggregationList as $nonHalalAggregation) {
                $result['aggregation']['other'] += abs($nonHalalAggregation->getAmount());
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
    private function fetchDistributionForBankAdmin(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new NonHalalFundingDistribution()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $nonHalalDistributionList = $queryBuilder
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
                        sprintf('%s.kind', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, NonHalalDistributionType::BANK_ADMIN)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($nonHalalDistributionList as $nonHalalDistribution) {
                $result['distribution']['bankAdmin'] += abs($nonHalalDistribution->getAmount());
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
    private function fetchDistributionForNonHalalFundingUsage(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new NonHalalFundingDistribution()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $nonHalalDistributionList = $queryBuilder
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
                        sprintf('%s.kind', $entities[1]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[1]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, NonHalalDistributionType::NON_HALAL_FUNDING_USAGE)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($nonHalalDistributionList as $nonHalalDistribution) {
                $result['distribution']['nonHalalFundingUsage'] += abs($nonHalalDistribution->getAmount());
            }
        }

        return;
    }
}
