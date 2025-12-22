<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\NonHalalFundingDistribution;
use Lazis\Api\Entity\NonHalalFundingReceive;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Report\NonHalalFundingChangeReport;
use Lazis\Api\Type\NonHalalDistribution as NonHalalDistributionType;
use Lazis\Api\Type\NonHalalFunding as NonHalalFundingType;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ReportNonHalalFundingChangeRepository extends AbstractRepository
{
    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     */
    public function generateReport(SchemaInterface $schema): array
    {
        $results = [];

        $this->aggregateReceptionByType(
            $schema,
            $results,
            NonHalalFundingType::BANK_INTEREST
        );

        $this->aggregateReceptionByType(
            $schema,
            $results,
            NonHalalFundingType::CURRENT_ACCOUNT_SERVICE
        );

        $this->aggregateReceptionByType(
            $schema,
            $results,
            NonHalalFundingType::OTHER
        );

        $this->aggregateDistributionByType(
            $schema,
            $results,
            NonHalalDistributionType::BANK_ADMIN
        );

        $this->aggregateDistributionByType(
            $schema,
            $results,
            NonHalalDistributionType::NON_HALAL_FUNDING_USAGE
        );

        return $results;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @param int $receptionType
     * @return void
     */
    private function aggregateReceptionByType(
        SchemaInterface $schema,
        array &$results,
        int $receptionType
    ): void {
        $entities = [new Organization(), new NonHalalFundingReceive()];
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
                    sprintf('%s.kind', $entities[1]->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, $receptionType)
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

            $mutator = $this->getReceptionMutatorByType($receptionType);

            if (null === $report) {
                $report = new NonHalalFundingChangeReport();
                $report->setYear(intval($year));

                call_user_func(
                    [$report->getNonHalalReception(), $mutator],
                    $rlist->getAmount()
                );

                $results[] = $report;
                continue;
            }

            call_user_func(
                [$report->getNonHalalReception(), $mutator],
                $rlist->getAmount()
            );
        }

        return;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @param int $distributionType
     * @return void
     */
    private function aggregateDistributionByType(
        SchemaInterface $schema,
        array &$results,
        int $distributionType
    ): void {
        $entities = [new Organization(), new NonHalalFundingDistribution()];
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
                    sprintf('%s.kind', $entities[1]->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, $distributionType)
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
                $report = new NonHalalFundingChangeReport();
                $report->setYear(intval($year));

                $amount = $rlist->getAmount() < 0
                    ? $rlist->getAmount() * -1
                    : $rlist->getAmount();

                call_user_func(
                    [$report->getNonHalalDistribution(), $mutator],
                    $amount
                );

                $results[] = $report;
                continue;
            }

            $amount = $rlist->getAmount() < 0
                ? $rlist->getAmount() * -1
                : $rlist->getAmount();

            call_user_func(
                [$report->getNonHalalDistribution(), $mutator],
                $amount
            );
        }

        return;
    }

    /**
     * @internal
     *
     * @param int $receptionType
     * @return string
     */
    private function getReceptionMutatorByType(int $receptionType): string
    {
        return match ($receptionType) {
            NonHalalFundingType::BANK_INTEREST => 'addBankInterestFund',
            NonHalalFundingType::CURRENT_ACCOUNT_SERVICE => 'addCurrentAccountServiceFund',
            NonHalalFundingType::OTHER => 'addOtherFund'
        };
    }

    /**
     * @internal
     *
     * @param int $distributionType
     * @return string
     */
    private function getDistributionMutatorByType(int $distributionType): string
    {
        return match ($distributionType) {
            NonHalalDistributionType::BANK_ADMIN => 'addBankAdmin',
            NonHalalDistributionType::NON_HALAL_FUNDING_USAGE => 'addNonHalalFundingUsage'
        };
    }
}
