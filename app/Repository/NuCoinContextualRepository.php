<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Donor;
use Lazis\Api\Entity\NuCoin;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Volunteer;
use Schnell\Http\FQL\InterceptorFactory;
use Schnell\Paginator\PageInterface;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinContextualRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @param mixed $refId
     * @return array
     */
    public function paginate($refId): array
    {
        $count = $this->count($refId);
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->resolvePaginationByRefId($refId, $page);

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $refId
     * @return int
     */
    public function count($refId): int
    {
        $entities = [
            new Organization(), new Volunteer(),
            new Donor(), new NuCoin()
        ];

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder = $queryBuilder
            ->select(
                sprintf(
                    'count(%s)',
                    $entities[3]->getQueryBuilderAlias()
                )
            )
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
                    '%s.id = %s.volunteer',
                    $entities[1]->getQueryBuilderAlias(),
                    $entities[2]->getQueryBuilderAlias()
                )
            )
            ->join(
                $entities[3]->getDqlName(),
                $entities[3]->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.donor',
                    $entities[2]->getQueryBuilderAlias(),
                    $entities[3]->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $refId);

        if (null === $this->getRequest()) {
            return $queryBuilder
                ->getQuery()
                ->getSingleScalarResult();
        }

        $interceptorFactory = new InterceptorFactory(
            $this->getRequest(),
            $queryBuilder,
            $entities[3]
        );

        $interceptor = $interceptorFactory->createInterceptor();

        if (null === $interceptor) {
            return $queryBuilder
                ->getQuery()
                ->getSingleScalarResult();
        }

        return $interceptor->intercept()
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param mixed $refId
     * @return array
     */
    private function resolvePaginationByRefId($refId, PageInterface $page): array
    {
        $entities = [
            new Organization(), new Volunteer(),
            new Donor(), new NuCoin()
        ];

        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder = $queryBuilder
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
                    '%s.id = %s.volunteer',
                    $entities[1]->getQueryBuilderAlias(),
                    $entities[2]->getQueryBuilderAlias()
                )
            )
            ->join(
                $entities[3]->getDqlName(),
                $entities[3]->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.donor',
                    $entities[2]->getQueryBuilderAlias(),
                    $entities[3]->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $refId);

        return $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginateFromQueryBuilder($queryBuilder, $entities[3]);
    }
}
