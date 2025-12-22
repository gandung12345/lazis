<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Mosque;
use Lazis\Api\Entity\OffBalanceSheet;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Report\OffBalanceSheetAggregateReport;
use Lazis\Api\Type\OffBalanceSheet as OffBalanceSheetType;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ReportOffBalanceSheetAggregateRepository extends AbstractRepository
{
    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     */
    public function generateReport(SchemaInterface $schema): array
    {
        $results = [];

        $this->collectByType($schema, $results, OffBalanceSheetType::ZAKAT_MAL);
        $this->collectByType($schema, $results, OffBalanceSheetType::ZAKAT_FITRAH);
        $this->collectByType($schema, $results, OffBalanceSheetType::INFAQ);
        $this->collectByType($schema, $results, OffBalanceSheetType::NATURA_INFAQ);
        $this->collectByType($schema, $results, OffBalanceSheetType::QURBAN);
        $this->collectByType($schema, $results, OffBalanceSheetType::FIDYAH);
        $this->collectByType($schema, $results, OffBalanceSheetType::DSKL);

        return $results;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @param int $type
     * @return void
     */
    private function collectByType(SchemaInterface $schema, array &$results, int $type): void
    {
        $entities = [new Organization(), new Mosque(), new OffBalanceSheet()];
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
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, $type)
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
            $mutator = $this->getMutatorByType($type);

            foreach ($results as &$result) {
                if ($result->getYear() === intval($year)) {
                    $report = $result;
                    break;
                }
            }

            if (null === $report) {
                $report = new OffBalanceSheetAggregateReport();
                $report->setYear(intval($year));

                call_user_func([$report, $mutator], $rlist->getAmount());

                $results[] = $report;
                continue;
            }

            call_user_func([$report, $mutator], $rlist->getAmount());
        }

        return;
    }

    /**
     * @internal
     *
     * @param int $type
     * @return string
     */
    private function getMutatorByType(int $type): string
    {
        return match ($type) {
            OffBalanceSheetType::ZAKAT_MAL => 'addZakatMaal',
            OffBalanceSheetType::ZAKAT_FITRAH => 'addZakatFitrah',
            OffBalanceSheetType::INFAQ => 'addInfaq',
            OffBalanceSheetType::NATURA_INFAQ => 'addInfaqNatura',
            OffBalanceSheetType::QURBAN => 'addQurban',
            OffBalanceSheetType::FIDYAH => 'addFidyah',
            OffBalanceSheetType::DSKL => 'addDskl'
        };
    }
}
