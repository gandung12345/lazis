<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Mosque;
use Lazis\Api\Entity\Organization;
use Schnell\Http\FQL\InterceptorFactory;
use Schnell\Paginator\PageInterface;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class MosqueContextualRepository extends AbstractRepository
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
        $entities = [new Organization(), new Mosque()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder = $queryBuilder
            ->select(
                sprintf(
                    'count(%s)',
                    $entities[1]->getQueryBuilderAlias()
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
            $entities[1]
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
        $entities = [new Organization(), new Mosque()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder = $queryBuilder
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
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $refId);

        return $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginateFromQueryBuilder($queryBuilder, $entities[1]);
    }
}
