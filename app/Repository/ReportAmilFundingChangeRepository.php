<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Amil;
use Lazis\Api\Entity\AmilFunding;
use Lazis\Api\Entity\AmilFundingUsage;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Organizer;
use Lazis\Api\Entity\Transaction;
use Lazis\Api\Entity\Wallet;
use Lazis\Api\Entity\Report\AmilFundingChangeReport;
use Lazis\Api\Type\AmilFunding as AmilFundingType;
use Lazis\Api\Type\AmilFundingDistribution as AmilFundingDistributionType;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ReportAmilFundingChangeRepository extends AbstractRepository
{
    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     */
    public function generateReport(SchemaInterface $schema): array
    {
        $result = [];

        $this->aggregateAmilFundingFromInfaq($schema, $result);
        $this->aggregateAmilFundingFromDskl($schema, $result);
        $this->aggregateAmilFundingFromZakat($schema, $result);
        $this->aggregateAmilFundingFromGrant($schema, $result);
        $this->aggregateAmilFundingFromOther($schema, $result);

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::SOCIAL_AND_EDUCATION_FUNDING
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::EMPLOYEE_EXPENSES
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::SALARY
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::OFFICE_EQUIPMENT_COST
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::OFFICE_STATIONERY_COST
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::INTERNET_COST
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::PHONE_BILL_COST
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::ELECTRIC_BILL_COST
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::TRANSPORTATION_COST
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::COMMUNICATION_COST
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::OFFICE_ASSET_MAINTENANCE_COST
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::FOOD_AND_BEVERAGE_COST
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::INSURANCE_COST
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::ADMIN_AND_COMMON_COST
        );

        $this->aggregateAmilFundingUsageByType(
            $schema,
            $result,
            AmilFundingDistributionType::DEPRECIATION_EXPENSE
        );

        return $result;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function aggregateAmilFundingFromInfaq(
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
                    $queryBuilder->expr()->literal('(infaq::amil-funding-cut)%')
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

            if ($report === null) {
                $report = new AmilFundingChangeReport();
                $report->setYear(intval($year));
                $report->getAmilFundingReception()
                    ->addAmilFundFromInfaq($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getAmilFundingReception()
                ->addAmilFundFromZakat($rlist->getAmount());
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
    private function aggregateAmilFundingFromDskl(
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

            if ($report === null) {
                $report = new AmilFundingChangeReport();
                $report->setYear(intval($year));
                $report->getAmilFundingReception()
                    ->addAmilFundFromDskl($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getAmilFundingReception()
                ->addAmilFundFromDskl($rlist->getAmount());
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
    private function aggregateAmilFundingFromZakat(
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

            if ($report === null) {
                $report = new AmilFundingChangeReport();
                $report->setYear(intval($year));
                $report->getAmilFundingReception()
                    ->addAmilFundFromZakat($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getAmilFundingReception()
                ->addAmilFundFromZakat($rlist->getAmount());
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
    private function aggregateAmilFundingFromGrant(
        SchemaInterface $schema,
        array &$results
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new AmilFunding()
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
                    sprintf('%s.fundingType', $entities[3]->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, AmilFundingType::GRANT_FUNDS)
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
                $report = new AmilFundingChangeReport();
                $report->setYear(intval($year));
                $report->getAmilFundingReception()
                    ->addAmilFundFromGrant($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getAmilFundingReception()
                ->addAmilFundFromGrant($rlist->getAmount());
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
    private function aggregateAmilFundingFromOther(
        SchemaInterface $schema,
        array &$results
    ): void {
        $entities = [
            new Organization(), new Organizer(),
            new Amil(), new AmilFunding()
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
                    sprintf('%s.fundingType', $entities[3]->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, AmilFundingType::OTHER_AMIL)
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
                $report = new AmilFundingChangeReport();
                $report->setYear(intval($year));
                $report->getAmilFundingReception()
                    ->addAmilFundFromOther($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getAmilFundingReception()
                ->addAmilFundFromOther($rlist->getAmount());
        }

        return;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @param int $usageType
     * @return void
     */
    private function aggregateAmilFundingUsageByType(
        SchemaInterface $schema,
        array &$results,
        int $usageType
    ): void {
        $entities = [new Organization(), new AmilFundingUsage()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $rlists = $queryBuilder
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
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, $usageType)
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
            $mutator = $this->getAmilFundingMutatorByUsageType($usageType);

            foreach ($results as &$result) {
                if ($result->getYear() === intval($year)) {
                    $report = $result;
                    break;
                }
            }

            if (null === $report) {
                $report = new AmilFundingChangeReport();
                $report->setYear(intval($year));

                call_user_func(
                    [$report->getAmilFundingUtilization(), $mutator],
                    abs($rlist->getAmount())
                );

                $results[] = $report;
                continue;
            }

            call_user_func(
                [$report->getAmilFundingUtilization(), $mutator],
                abs($rlist->getAmount())
            );
        }

        return;
    }

    /**
     * @internal
     *
     * @param int $usageType
     * @return string
     */
    private function getAmilFundingMutatorByUsageType(int $usageType): ?string
    {
        switch ($usageType) {
            case AmilFundingDistributionType::SOCIAL_AND_EDUCATION_FUNDING:
                return 'addSocializationAndEducationCost';
            case AmilFundingDistributionType::EMPLOYEE_EXPENSES:
                return 'addEmployeeExpenseCost';
            case AmilFundingDistributionType::SALARY:
                return 'addEmployeeSalary';
            case AmilFundingDistributionType::OFFICE_EQUIPMENT_COST:
                return 'addOfficeUtilityCost';
            case AmilFundingDistributionType::OFFICE_STATIONERY_COST:
                return 'addOfficeStationeryCost';
            case AmilFundingDistributionType::INTERNET_COST:
                return 'addInternetCost';
            case AmilFundingDistributionType::PHONE_BILL_COST:
                return 'addTelephoneCost';
            case AmilFundingDistributionType::ELECTRIC_BILL_COST:
                return 'addElectricityCost';
            case AmilFundingDistributionType::TRANSPORTATION_COST:
                return 'addTransportationCost';
            case AmilFundingDistributionType::COMMUNICATION_COST:
                return 'addCommunicationCost';
            case AmilFundingDistributionType::OFFICE_ASSET_MAINTENANCE_COST:
                return 'addOfficeAssetMaintenanceCost';
            case AmilFundingDistributionType::FOOD_AND_BEVERAGE_COST:
                return 'addConsumptionCost';
            case AmilFundingDistributionType::INSURANCE_COST:
                return 'addInsuranceCost';
            case AmilFundingDistributionType::ADMIN_AND_COMMON_COST:
                return 'addAdminAndCommonCost';
            case AmilFundingDistributionType::DEPRECIATION_EXPENSE:
                return 'addDeprecationExpense';
        }

        return null;
    }
}
