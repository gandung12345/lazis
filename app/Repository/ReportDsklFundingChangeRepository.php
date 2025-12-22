<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Amil;
use Lazis\Api\Entity\Donee;
use Lazis\Api\Entity\Dskl;
use Lazis\Api\Entity\InfaqDistribution;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Organizer;
use Lazis\Api\Entity\Transaction;
use Lazis\Api\Entity\Wallet;
use Lazis\Api\Entity\Report\DsklFundingChangeReport;
use Lazis\Api\Type\Dskl as DsklType;
use Lazis\Api\Type\Wallet as WalletType;
use Lazis\Api\Type\ZakatDistribution as ZakatDistributionType;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ReportDsklFundingChangeRepository extends AbstractRepository
{
    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     */
    public function generateReport(SchemaInterface $schema): array
    {
        $results = [];

        $this->aggregateDsklFromBpkh($schema, $results);
        $this->aggregateDsklFromQurban($schema, $results);
        $this->aggregateDsklFromFidyah($schema, $results);
        $this->aggregateAmilAllocationFund($schema, $results);

        $this->aggregateDsklDistributionByType(
            $schema,
            $results,
            ZakatDistributionType::NU_CARE_SMART
        );

        $this->aggregateDsklDistributionByType(
            $schema,
            $results,
            ZakatDistributionType::NU_CARE_EMPOWERED
        );

        $this->aggregateDsklDistributionByType(
            $schema,
            $results,
            ZakatDistributionType::NU_CARE_HEALTHY
        );

        $this->aggregateDsklDistributionByType(
            $schema,
            $results,
            ZakatDistributionType::NU_CARE_GREEN
        );

        $this->aggregateDsklDistributionByType(
            $schema,
            $results,
            ZakatDistributionType::NU_CARE_PEACE
        );

        // TODO: working on it later after refixing
        // qurban logic flow :)
        $this->aggregateDsklDistributionByType(
            $schema,
            $results,
            ZakatDistributionType::NU_CARE_QURBAN
        );

        return $results;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function aggregateDsklFromBpkh(SchemaInterface $schema, array &$results): void
    {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Dskl()
        ];

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $rlists = $queryBuilder
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
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, DsklType::BPKH)
            ->getQuery()
            ->getResult();

        if (sizeof($rlists) === 0) {
            return;
        }

        foreach ($rlists as $rlist) {
            $year = $rlist->getDate()
                ->withFormat('Y')
                ->stringify();

            if (
                intval($year) < $schema->getYear()->getStart() ||
                intval($year) > $schema->getYear()->getEnd()
            ) {
                continue;
            }

            $report = null;

            foreach ($results as &$result) {
                if ($result->getYear() === intval($year)) {
                    $report = $result;
                    break;
                }
            }

            if ($report === null) {
                $report = new DsklFundingChangeReport();
                $report->setYear(intval($year));
                $report->getDsklReception()
                    ->addBpkhFund($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getDsklReception()
                ->addBpkhFund($rlist->getAmount());
        }

        return;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function aggregateDsklFromQurban(SchemaInterface $schema, array &$results): void
    {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Dskl()
        ];

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $rlists = $queryBuilder
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
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, DsklType::QURBAN)
            ->getQuery()
            ->getResult();

        if (sizeof($rlists) === 0) {
            return;
        }

        foreach ($rlists as $rlist) {
            $year = $rlist->getDate()
                ->withFormat('Y')
                ->stringify();

            if (
                intval($year) < $schema->getYear()->getStart() ||
                intval($year) > $schema->getYear()->getEnd()
            ) {
                continue;
            }

            $report = null;

            foreach ($results as &$result) {
                if ($result->getYear() === intval($year)) {
                    $report = $result;
                    break;
                }
            }

            if (null === $report) {
                $report = new DsklFundingChangeReport();
                $report->setYear(intval($year));
                $report->getDsklReception()
                    ->addQurbanFund($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getDsklReception()
                ->addQurbanFund($rlist->getAmount());
        }

        return;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function aggregateDsklFromFidyah(SchemaInterface $schema, array &$results): void
    {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Dskl()
        ];

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $rlists = $queryBuilder
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
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, DsklType::FIDYAH)
            ->getQuery()
            ->getResult();

        if (sizeof($rlists) === 0) {
            return;
        }

        foreach ($rlists as $rlist) {
            $year = $rlist->getDate()
                ->withFormat('Y')
                ->stringify();

            if (
                intval($year) < $schema->getYear()->getStart() ||
                intval($year) > $schema->getYear()->getEnd()
            ) {
                continue;
            }

            $report = null;

            foreach ($results as &$result) {
                if ($result->getYear() === intval($year)) {
                    $report = $result;
                    break;
                }
            }

            if (null === $report) {
                $report = new DsklFundingChangeReport();
                $report->setYear(intval($year));
                $report->getDsklReception()
                    ->addFidyahFund($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getDsklReception()
                ->addFidyahFund($rlist->getAmount());
        }

        return;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function aggregateDsklDistributionByType(
        SchemaInterface $schema,
        array &$results,
        int $distributionType
    ): void {
        $entities = [new Organization(), new Donee(), new InfaqDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $rlists = $queryBuilder
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
                    sprintf('%s.fundingResource', $entities[2]->getQueryBuilderAlias()),
                    '?2'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.program', $entities[2]->getQueryBuilderAlias()),
                    '?3'
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, WalletType::ORGANIZATION_SOCIAL_FUNDING)
            ->setParameter(3, $distributionType)
            ->getQuery()
            ->getResult();

        if (sizeof($rlists) === 0) {
            return;
        }

        foreach ($rlists as $rlist) {
            $year = $rlist->getDate()
                ->withFormat('Y')
                ->stringify();

            if (
                intval($year) < $schema->getYear()->getStart() ||
                intval($year) > $schema->getYear()->getEnd()
            ) {
                continue;
            }

            $report = null;

            foreach ($results as &$result) {
                if ($result->getYear() === intval($year)) {
                    $report = $result;
                    break;
                }
            }

            $mutator = $this->getDistributionMutatorByType($distributionType);

            if (null === $report) {
                $report = new DsklFundingChangeReport();
                $report->setYear(intval($year));

                call_user_func(
                    [$report->getDsklDistribution(), $mutator],
                    $rlist->getAmount()
                );

                $results[] = $report;
                continue;
            }

            call_user_func(
                [$report->getDsklDistribution(), $mutator],
                $rlist->getAmount()
            );
        }

        return;
    }

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function aggregateAmilAllocationFund(
        SchemaInterface $schema,
        array &$results
    ): void {
        $entities = [new Organization(), new Wallet(), new Transaction()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $rlists = $queryBuilder
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
                $queryBuilder->expr()->like(
                    sprintf('%s.description', $entities[2]->getQueryBuilderAlias()),
                    $queryBuilder->expr()->literal('(dskl::amil-funding-cut)%')
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->getQuery()
            ->getResult();

        if (sizeof($rlists) === 0) {
            return;
        }

        foreach ($rlists as $rlist) {
            $year = $rlist->getDate()
                ->withFormat('Y')
                ->stringify();

            if (
                intval($year) < $schema->getYear()->getStart() ||
                intval($year) > $schema->getYear()->getEnd()
            ) {
                continue;
            }

            $report = null;

            foreach ($results as &$result) {
                if ($result->getYear() === intval($year)) {
                    $report = $result;
                    break;
                }
            }

            if (null === $report) {
                $report = new DsklFundingChangeReport();
                $report->setYear(intval($year));
                $report->getDsklDistribution()
                    ->addAmilAllocationFund($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getDsklDistribution()
                ->addAmilAllocationFund($rlist->getAmount());
        }

        return;
    }

    /**
     * @param int $distributionType
     * @return string
     */
    private function getDistributionMutatorByType(int $distributionType): string
    {
        return match ($distributionType) {
            ZakatDistributionType::NU_CARE_SMART => 'addNuSmartFund',
            ZakatDistributionType::NU_CARE_EMPOWERED => 'addNuEmpoweredFund',
            ZakatDistributionType::NU_CARE_HEALTHY => 'addNuHealthFund',
            ZakatDistributionType::NU_CARE_GREEN => 'addNuGreenFund',
            ZakatDistributionType::NU_CARE_PEACE => 'addNuPeaceFund',
            ZakatDistributionType::NU_CARE_QURBAN => 'addNuPeaceWithQurbanFund',
        };
    }
}
