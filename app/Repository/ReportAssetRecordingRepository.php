<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\AssetRecording;
use Lazis\Api\Entity\Report\AssetRecordingReport;
use Lazis\Api\Entity\Report\Components\CurrentAsset;
use Lazis\Api\Entity\Report\Components\NonCurrentAsset;
use Lazis\Api\Type\AssetRecording as AssetRecordingType;
use Schnell\Entity\EntityInterface;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ReportAssetRecordingRepository extends AbstractRepository
{
    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     */
    public function generateReport(SchemaInterface $schema): array
    {
        $result = [];

        $this->aggregateCurrentAsset($schema, $result);
        $this->aggregateNonCurrentAsset($schema, $result);

        return $result;
    }

    private function aggregateCurrentAsset(
        SchemaInterface $schema,
        array &$results
    ): void {
        $this->aggregateAssetByKind(
            $schema,
            $results,
            AssetRecordingType::CURRENT_ASSET
        );
    }

    private function aggregateNonCurrentAsset(
        SchemaInterface $schema,
        array &$results
    ): void {
        $this->aggregateAssetByKind(
            $schema,
            $results,
            AssetRecordingType::NON_CURRENT_ASSET
        );
    }

    private function aggregateAssetByKind(
        SchemaInterface $schema,
        array &$results,
        int $kind
    ): void {
        $entities = [new Organization(), new AssetRecording()];
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
            ->setParameter(2, $kind)
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
                $report = new AssetRecordingReport();
                $report->setYear(intval($year));

                $this->callMutatorByKind(
                    $report,
                    $this->buildAssetByKind($rlist, $kind),
                    $kind
                );

                $results[] = $report;
                continue;
            }

            $this->callMutatorByKind(
                $report,
                $this->buildAssetByKind($rlist, $kind),
                $kind
            );
        }

        return;
    }

    private function getMutatorByKind(int $kind): string
    {
        return match ($kind) {
            AssetRecordingType::CURRENT_ASSET => 'addCurrentAsset',
            AssetRecordingType::NON_CURRENT_ASSET => 'addNonCurrentAsset'
        };
    }

    private function callMutatorByKind(
        EntityInterface &$report,
        EntityInterface $assetComponent,
        int $kind
    ): void {
        call_user_func(
            [$report, $this->getMutatorByKind($kind)],
            $assetComponent
        );
    }

    private function buildAssetByKind(EntityInterface $entity, int $kind): EntityInterface
    {
        return match ($kind) {
            AssetRecordingType::CURRENT_ASSET => new CurrentAsset(
                $entity->getName(),
                $entity->getPrice()
            ),
            AssetRecordingType::NON_CURRENT_ASSET => new NonCurrentAsset(
                $entity->getName(),
                $entity->getPrice()
            )
        };
    }
}
