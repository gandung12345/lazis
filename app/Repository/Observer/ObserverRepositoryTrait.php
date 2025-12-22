<?php

declare(strict_types=1);

namespace Lazis\Api\Repository\Observer;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Composite;
use Schnell\Entity\EntityInterface;

use function sprintf;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
trait ObserverRepositoryTrait
{
    /**
     * @internal
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Schnell\Entity\EntityInterface $entity
     * @return \Doctrine\ORM\Query\Expr\Composite
     */
    private function buildObserverPredicate(
        QueryBuilder $queryBuilder,
        EntityInterface $entity
    ): Composite {
        return $queryBuilder->expr()->andX(
            $queryBuilder->expr()->gte(
                sprintf('%s.scope', $entity->getQueryBuilderAlias()),
                '?1'
            ),
            $queryBuilder->expr()->lte(
                sprintf('%s.scope', $entity->getQueryBuilderAlias()),
                '?2'
            ),
            $queryBuilder->expr()->eq(
                sprintf('%s.district', $entity->getQueryBuilderAlias()),
                '?3'
            ),
            $queryBuilder->expr()->eq(
                sprintf('%s.village', $entity->getQueryBuilderAlias()),
                '?4'
            )
        );
    }
}
