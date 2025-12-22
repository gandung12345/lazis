<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Amil;
use Lazis\Api\Entity\Donee;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Organizer;
use Lazis\Api\Entity\Transaction;
use Lazis\Api\Entity\Wallet;
use Lazis\Api\Entity\Zakat;
use Lazis\Api\Entity\ZakatDistribution;
use Lazis\Api\Entity\Report\ZakatFundingChangeReport;
use Lazis\Api\Type\Asnaf as AsnafType;
use Lazis\Api\Type\Muzakki as MuzakkiType;
use Lazis\Api\Type\Zakat as ZakatType;
use Lazis\Api\Type\ZakatDistribution as ZakatDistributionType;
use Schnell\Entity\EntityInterface;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;

use function abs;
use function call_user_func;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ReportZakatFundingChangeRepository extends AbstractRepository
{
    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     */
    public function generateReport(SchemaInterface $schema): array
    {
        $result = [];

        $this->aggregateZakatReception($schema, $result);
        $this->aggregateAsnafBasedDistribution($schema, $result);
        $this->aggregateProgramBasedDistribution($schema, $result);

        return $result;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function aggregateZakatReception(SchemaInterface $schema, array &$results): void
    {
        $this->aggregateZakatMaalPersonal($schema, $results);
        $this->aggregateZakatMaalCollective($schema, $results);
        $this->aggregateZakatFitrah($schema, $results);
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function aggregateZakatMaalPersonal(SchemaInterface $schema, array &$results): void
    {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Zakat()
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
                    sprintf('%s.muzakki', $entities[3]->getQueryBuilderAlias()),
                    '?2'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.type', $entities[3]->getQueryBuilderAlias()),
                    '?3'
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, MuzakkiType::PERSONAL)
            ->setParameter(3, ZakatType::MAAL)
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
                $report = new ZakatFundingChangeReport();
                $report->setYear(intval($year));
                $report->getZakatReception()
                    ->addZakatMaalPersonal($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getZakatReception()
                ->addZakatMaalPersonal($rlist->getAmount());
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
    private function aggregateZakatMaalCollective(
        SchemaInterface $schema,
        array &$results
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Zakat()
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
                    sprintf('%s.muzakki', $entities[3]->getQueryBuilderAlias()),
                    '?2'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.type', $entities[3]->getQueryBuilderAlias()),
                    '?3'
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, MuzakkiType::COLLECTIVE)
            ->setParameter(3, ZakatType::MAAL)
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
                $report = new ZakatFundingChangeReport();
                $report->setYear(intval($year));
                $report->getZakatReception()
                    ->addZakatMaalCollective($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getZakatReception()
                ->addZakatMaalCollective($rlist->getAmount());
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
    private function aggregateZakatFitrah(SchemaInterface $schema, array &$results): void
    {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new Zakat()
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
                    sprintf('%s.muzakki', $entities[3]->getQueryBuilderAlias()),
                    '?2'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.type', $entities[3]->getQueryBuilderAlias()),
                    '?3'
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, MuzakkiType::PERSONAL)
            ->setParameter(3, ZakatType::FITRAH)
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
                $report = new ZakatFundingChangeReport();
                $report->setYear(intval($year));
                $report->getZakatReception()
                    ->addZakatFitrah($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getZakatReception()
                ->addZakatFitrah($rlist->getAmount());
        }

        return;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @param int $asnafType
     * @return void
     */
    private function aggregateAsnafBasedDistributionByType(
        SchemaInterface $schema,
        array &$results,
        int $asnafType
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
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
                    sprintf('%s.asnaf', $entities[1]->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, $asnafType)
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
                $report = new ZakatFundingChangeReport();
                $report->setYear(intval($year));

                $ephemeral = $report->getAsnafBasedDistribution();

                $this->callAsnafDistMutatorByType($ephemeral, $rlist, $asnafType);

                $results[] = $report;
                continue;
            }

            $ephemeral = $report->getAsnafBasedDistribution();

            $this->callAsnafDistMutatorByType($ephemeral, $rlist, $asnafType);
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
    private function aggregateAmilAllocationFromZakat(
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
                    $queryBuilder->expr()->literal('(zakat::amil-funding-cut)%')
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
                $report = new ZakatFundingChangeReport();
                $report->setYear(intval($year));
                $report->getAsnafBasedDistribution()
                    ->addAmilAllocationFund($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getAsnafBasedDistribution()
                ->addAmilAllocationFund($rlist->getAmount());
        }

        return;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @param int $programType
     * @return void
     */
    private function aggregateProgramBasedDistributionByType(
        SchemaInterface $schema,
        array &$results,
        int $programType
    ): void {
        $entities = [new Organization(), new Donee(), new ZakatDistribution()];
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
                    sprintf('%s.program', $entities[2]->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, $programType)
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
                $report = new ZakatFundingChangeReport();
                $report->setYear(intval($year));

                $ephemeral = $report->getProgramBasedDistribution();

                $this->callProgramDistMutatorByType(
                    $ephemeral,
                    $rlist,
                    $programType
                );

                $results[] = $report;
                continue;
            }

            $ephemeral = $report->getProgramBasedDistribution();

            $this->callProgramDistMutatorByType(
                $ephemeral,
                $rlist,
                $programType
            );
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
    private function aggregateAsnafBasedDistribution(
        SchemaInterface $schema,
        array &$results
    ): void {
        $this->aggregateAsnafBasedDistributionByType($schema, $results, AsnafType::FAKIR);
        $this->aggregateAsnafBasedDistributionByType($schema, $results, AsnafType::POOR);
        $this->aggregateAsnafBasedDistributionByType($schema, $results, AsnafType::MUALAF);
        $this->aggregateAsnafBasedDistributionByType($schema, $results, AsnafType::RIQAB);
        $this->aggregateAsnafBasedDistributionByType($schema, $results, AsnafType::GHARIM);
        $this->aggregateAsnafBasedDistributionByType($schema, $results, AsnafType::FISABILILLAH);
        $this->aggregateAsnafBasedDistributionByType($schema, $results, AsnafType::IBNU_SABIL);
        $this->aggregateAmilAllocationFromZakat($schema, $results);
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function aggregateProgramBasedDistribution(
        SchemaInterface $schema,
        array &$results
    ): void {
        $this->aggregateProgramBasedDistributionByType(
            $schema,
            $results,
            ZakatDistributionType::NU_CARE_SMART
        );

        $this->aggregateProgramBasedDistributionByType(
            $schema,
            $results,
            ZakatDistributionType::NU_CARE_EMPOWERED
        );

        $this->aggregateProgramBasedDistributionByType(
            $schema,
            $results,
            ZakatDistributionType::NU_CARE_HEALTHY
        );

        $this->aggregateProgramBasedDistributionByType(
            $schema,
            $results,
            ZakatDistributionType::NU_CARE_GREEN
        );

        $this->aggregateProgramBasedDistributionByType(
            $schema,
            $results,
            ZakatDistributionType::NU_CARE_PEACE
        );
    }

    /**
     * @internal
     *
     * @param int $asnafType
     * @return string
     */
    private function getAsnafDistMutatorByType(int $asnafType): string
    {
        return match ($asnafType) {
            AsnafType::FAKIR => 'addFakirFund',
            AsnafType::POOR => 'addPoorFund',
            AsnafType::MUALAF => 'addMualafFund',
            AsnafType::RIQAB => 'addRiqabFund',
            AsnafType::GHARIM => 'addGharimFund',
            AsnafType::FISABILILLAH => 'addFisabilillahFund',
            AsnafType::IBNU_SABIL => 'addIbnuSabilFund'
        };
    }

    /**
     * @internal
     *
     * @param int $programType
     * @return string
     */
    private function getProgramDistMutatorByType(int $programType): string
    {
        return match ($programType) {
            ZakatDistributionType::NU_CARE_SMART => 'addNuSmartFund',
            ZakatDistributionType::NU_CARE_EMPOWERED => 'addNuEmpoweredFund',
            ZakatDistributionType::NU_CARE_HEALTHY => 'addNuHealthFund',
            ZakatDistributionType::NU_CARE_GREEN => 'addNuGreenFund',
            ZakatDistributionType::NU_CARE_PEACE => 'addNuPeaceFund'
        };
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\EntityInterface &$report
     * @param \Schnell\Entity\EntityInterface $entity
     * @param int $asnafType
     * @return void
     */
    private function callAsnafDistMutatorByType(
        EntityInterface &$report,
        EntityInterface $entity,
        int $asnafType
    ): void {
        call_user_func(
            [$report, $this->getAsnafDistMutatorByType($asnafType)],
            abs($entity->getAmount())
        );
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\EntityInterface &$report
     * @param \Schnell\Entity\EntityInterface $entity
     * @param int $programType
     * @return void
     */
    private function callProgramDistMutatorByType(
        EntityInterface &$report,
        EntityInterface $entity,
        int $programType
    ): void {
        call_user_func(
            [$report, $this->getProgramDistMutatorByType($programType)],
            abs($entity->getAmount())
        );
    }
}
