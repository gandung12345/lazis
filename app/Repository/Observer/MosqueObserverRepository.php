<?php

declare(strict_types=1);

namespace Lazis\Api\Repository\Observer;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Mosque;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Repository\RepositoryTrait;
use Lazis\Api\Type\Scope as ScopeType;
use Schnell\Http\FQL\InterceptorFactory;
use Schnell\Paginator\PageInterface;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class MosqueObserverRepository extends AbstractRepository
{
    use RepositoryTrait;
    use ObserverRepositoryTrait;

    /**
     * @param int $scope
     * @param string $district
     * @param string $village
     * @return int
     */
    public function count(int $scope, string $district, string $village): int
    {
        $entities = [new Organization(), new Mosque()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder = $queryBuilder
            ->select(sprintf('count(%s)', $entities[1]->getQueryBuilderAlias()))
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
            ->where($this->buildObserverPredicate($queryBuilder, $entities[0]))
            ->setParameter(1, $scope)
            ->setParameter(2, ScopeType::TWIG)
            ->setParameter(3, $district)
            ->setParameter(4, $village);

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
     * @param int $scope
     * @param string $district
     * @param string $village
     * @return array
     */
    public function paginate(int $scope, string $district, string $village): array
    {
        $count = $this->count($scope, $district, $village);
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->resolvePaginationByMultipleConstraint(
            $page,
            $scope,
            $district,
            $village
        );

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @internal
     *
     * @param \Schnell\Paginator\PageInterface $page
     * @param int $scope
     * @param string $district
     * @param string $village
     * @return array
     */
    private function resolvePaginationByMultipleConstraint(
        PageInterface $page,
        int $scope,
        string $district,
        string $village
    ): array {
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
            ->where($this->buildObserverPredicate($queryBuilder, $entities[0]))
            ->setParameter(1, $scope)
            ->setParameter(2, ScopeType::TWIG)
            ->setParameter(3, $district)
            ->setParameter(4, $village);

        return $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginateFromQueryBuilder($queryBuilder, $entities[1]);
    }
}
