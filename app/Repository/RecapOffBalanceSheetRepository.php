<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use DateTimeImmutable;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Mosque;
use Lazis\Api\Entity\OffBalanceSheet;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Repository\Exception\RepositoryException;
use Lazis\Api\Type\OffBalanceSheet as OffBalanceSheetType;
use Lazis\Api\Type\Scope as ScopeType;
use Schnell\Entity\EntityInterface;
use Schnell\Repository\AbstractRepository;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class RecapOffBalanceSheetRepository extends AbstractRepository
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
            'zakatMaal' => 0,
            'zakatFitrah' => 0,
            'infaq' => 0,
            'naturaInfaq' => 0,
            'qurban' => 0,
            'fidyah' => 0,
            'dskl' => 0
        ];

        $this->getDistributionForZakatMaal($year, $organizationList, $result);
        $this->getDistributionForZakatFitrah($year, $organizationList, $result);
        $this->getDistributionForInfaq($year, $organizationList, $result);
        $this->getDistributionForNaturaInfaq($year, $organizationList, $result);
        $this->getDistributionForQurban($year, $organizationList, $result);
        $this->getDistributionForFidyah($year, $organizationList, $result);
        $this->getDistributionForDskl($year, $organizationList, $result);

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
    private function getDistributionForZakatMaal(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Mosque(), new OffBalanceSheet()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $offBalanceSheetList = $queryBuilder
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
                        '%s.id = %s.mosque',
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
                        sprintf('%s.kind', $entities[2]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, OffBalanceSheetType::ZAKAT_MAL)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($offBalanceSheetList as $offBalanceSheet) {
                $result['zakatMaal'] += abs($offBalanceSheet->getAmount());
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
    private function getDistributionForZakatFitrah(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Mosque(), new OffBalanceSheet()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $offBalanceSheetList = $queryBuilder
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
                        '%s.id = %s.mosque',
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
                        sprintf('%s.kind', $entities[2]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, OffBalanceSheetType::ZAKAT_FITRAH)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($offBalanceSheetList as $offBalanceSheet) {
                $result['zakatFitrah'] += abs($offBalanceSheet->getAmount());
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
    private function getDistributionForInfaq(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Mosque(), new OffBalanceSheet()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $offBalanceSheetList = $queryBuilder
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
                        '%s.id = %s.mosque',
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
                        sprintf('%s.kind', $entities[2]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, OffBalanceSheetType::INFAQ)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($offBalanceSheetList as $offBalanceSheet) {
                $result['infaq'] += abs($offBalanceSheet->getAmount());
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
    private function getDistributionForNaturaInfaq(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Mosque(), new OffBalanceSheet()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $offBalanceSheetList = $queryBuilder
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
                        '%s.id = %s.mosque',
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
                        sprintf('%s.kind', $entities[2]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, OffBalanceSheetType::NATURA_INFAQ)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($offBalanceSheetList as $offBalanceSheet) {
                $result['naturaInfaq'] += abs($offBalanceSheet->getAmount());
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
    private function getDistributionForQurban(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Mosque(), new OffBalanceSheet()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $offBalanceSheetList = $queryBuilder
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
                        '%s.id = %s.mosque',
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
                        sprintf('%s.kind', $entities[2]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, OffBalanceSheetType::QURBAN)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($offBalanceSheetList as $offBalanceSheet) {
                $result['qurban'] += abs($offBalanceSheet->getAmount());
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
    private function getDistributionForFidyah(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Mosque(), new OffBalanceSheet()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $offBalanceSheetList = $queryBuilder
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
                        '%s.id = %s.mosque',
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
                        sprintf('%s.kind', $entities[2]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, OffBalanceSheetType::FIDYAH)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($offBalanceSheetList as $offBalanceSheet) {
                $result['fidyah'] += abs($offBalanceSheet->getAmount());
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
    private function getDistributionForDskl(
        string $year,
        array $organizationList,
        array &$result
    ): void {
        $entities = [new Organization(), new Mosque(), new OffBalanceSheet()];

        $startTz = new DateTimeImmutable(sprintf('%s-01-01T00:00:00', $year));
        $endTz = new DateTimeImmutable(sprintf('%s-12-31T23:59:59', $year));

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        foreach ($organizationList as $organization) {
            $offBalanceSheetList = $queryBuilder
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
                        '%s.id = %s.mosque',
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
                        sprintf('%s.kind', $entities[2]->getQueryBuilderAlias()),
                        '?2'
                    )
                ))
                ->andWhere(sprintf('%s.date BETWEEN ?3 AND ?4', $entities[2]->getQueryBuilderAlias()))
                ->setParameter(1, $organization->getId())
                ->setParameter(2, OffBalanceSheetType::DSKL)
                ->setParameter(3, $startTz)
                ->setParameter(4, $endTz)
                ->getQuery()
                ->getResult();

            foreach ($offBalanceSheetList as $offBalanceSheet) {
                $result['dskl'] += abs($offBalanceSheet->getAmount());
            }
        }

        return;
    }
}
