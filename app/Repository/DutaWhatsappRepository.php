<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Lazis\Api\Entity\DutaWhatsapp;
use Schnell\Entity\EntityInterface;
use Schnell\Hydrator\MapHydrator;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DutaWhatsappRepository extends AbstractRepository
{
    use RepositoryTrait;

    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new DutaWhatsapp());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new DutaWhatsapp());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    public function getById($id, bool $hydrated = false): EntityInterface|array|null
    {
        $entity = $this
            ->getMapper()
            ->find(new DutaWhatsapp(), $id);

        if (null === $entity) {
            return null;
        }

        return false === $hydrated
            ? $entity
            : $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    public function create(SchemaInterface $schema, bool $hydrated = true): EntityInterface|array
    {
        $dutaWhatsapp = new DutaWhatsapp();
        $dutaWhatsapp->setId(Uuid::v7()->toString());

        $entity = $this->getMapper()
            ->withHydrator($hydrated ? new MapHydrator() : null)
            ->create($schema, $dutaWhatsapp);

        return $entity;
    }

    private function getRecordCount(): int
    {
        $entity = new DutaWhatsapp();
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $resultCount = $queryBuilder
            ->select(
                sprintf(
                    'count(%s)',
                    $entity->getQueryBuilderAlias()
                )
            )
            ->from($entity->getDqlName(), $entity->getQueryBuilderAlias())
            ->getQuery()
            ->getSingleScalarResult();

        return $resultCount;
    }

    public function getLatest(bool $hydrated = false): EntityInterface|array
    {
        $resultCount = $this->getRecordCount();
        $entity = new DutaWhatsapp();
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $results = $queryBuilder
            ->select($entity->getQueryBuilderAlias())
            ->from($entity->getDqlName(), $entity->getQueryBuilderAlias())
            ->setFirstResult($resultCount === 0 ? 0 : $resultCount - 1)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (sizeof($results) === 0) {
            return $hydrated ? [] : null;
        }

        return $hydrated ? MapHydrator::create()->hydrate($results[0]) : $results[0];
    }
}
