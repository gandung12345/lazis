<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Repository\Exception\RepositoryException;
use Schnell\Entity\EntityInterface;
use Schnell\Hydrator\MapHydrator;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
trait RepositoryTrait
{
    /**
     * @param array $result
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     */
    private function hydrateListWithParent(
        array $result,
        ServerRequestInterface $request
    ): array {
        $results = [];
        $showParentActive = $this->isShowParentActive($request);

        foreach ($result as $entity) {
            $results[] = $showParentActive
                ? $this->hydrateParent($entity)
                : MapHydrator::create()->hydrate($entity);
        }

        return $results;
    }

    /**
     * @param array $result
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     */
    private function hydrateEntityWithParent(
        EntityInterface $entity,
        ServerRequestInterface $request
    ): array {
        return $this->isShowParentActive($request)
            ? $this->hydrateParent($entity)
            : MapHydrator::create()->hydrate($entity);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return bool
     */
    private function isShowParentActive(ServerRequestInterface $request): bool
    {
        $queryParams = $request->getQueryParams();

        if (
            isset($queryParams['showParent']) &&
            false === in_array($queryParams['showParent'], ['true', 'false'], true)
        ) {
            throw new RepositoryException(
                $request,
                "'showParent' query string must be either 'true' or 'false'."
            );
        }

        if (
            !isset($queryParams['showParent']) ||
            $queryParams['showParent'] === 'false'
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param \Schnell\Entity\EntityInterface $entity
     * @return array
     */
    private function hydrateParent(EntityInterface $entity): array
    {
        return $this->mergeAndPopulateParent(
            $entity,
            MapHydrator::create()->hydrate($entity)
        );
    }

    /**
     * @param \Schnell\Entity\EntityInterface $entity
     * @param array $results
     * @return array
     */
    private function mergeAndPopulateParent(EntityInterface $entity, array $results): array
    {
        $newResults = $results;
        $reflection = new ReflectionClass($entity);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $attributeNames = array_map(
                function (ReflectionAttribute $attribute): string {
                    return $attribute->getName();
                },
                $property->getAttributes()
            );

            if (!in_array(JoinColumn::class, $attributeNames, true)) {
                continue;
            }

            $key = sprintf('@%s', $property->getName());
            $parent = call_user_func([$entity, sprintf('get%s', ucfirst($property->getName()))]);

            if (null === $parent) {
                continue;
            }

            $result = $this->fetchParent($parent, $entity, $property->getName());
            $newResults[$key] = MapHydrator::create()->hydrate($result);
        }

        return $newResults;
    }

    private function fetchParent(
        EntityInterface $parent,
        EntityInterface $child,
        string $parentPropertyName
    ): ?EntityInterface {
        $entityManager = $this->getMapper()
            ->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder
            ->select($parent->getQueryBuilderAlias())
            ->from($parent->getDqlName(), $parent->getQueryBuilderAlias())
            ->join(
                $child->getDqlName(),
                $child->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.%s',
                    $parent->getQueryBuilderAlias(),
                    $child->getQueryBuilderAlias(),
                    $parentPropertyName
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $parent->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $parent->getId())
            ->getQuery()
            ->getResult();

        if (sizeof($result) !== 1) {
            return null;
        }

        return $result[0];
    }
}
