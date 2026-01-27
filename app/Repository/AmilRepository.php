<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Amil;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Organizer;
use Schnell\Entity\EntityInterface;
use Schnell\Hydrator\ArrayHydrator;
use Schnell\Hydrator\MapHydrator;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class AmilRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new Amil());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withPage($page)
            ->withRequest($this->getRequest())
            ->paginate(new Amil());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new Amil(), $id);

        if (null === $entity) {
            return null;
        }

        return $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function create($refId, SchemaInterface $schema): EntityInterface|array|null
    {
        $amil = new Amil();
        $amil->setId(Uuid::v7()->toString());

        $entity = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->createReferenced(
                $refId,
                $schema,
                $amil,
                new Organizer()
            );

        return $entity;
    }

    /**
     * @param mixed $id
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function update($id, SchemaInterface $schema): EntityInterface|array|null
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->update($id, $schema, new Amil());

        return $result;
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function remove($id): EntityInterface|array|null
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->remove($id, new Amil());

        return $result;
    }

    /**
     * @param string $amilId
     * @param bool $hydrated
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function getOrganization(string $amilId, bool $hydrated = false): EntityInterface|array|null
    {
        $entities = [new Organization(), new Organizer(), new Amil()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder
            ->select($entities[0]->getQueryBuilderAlias())
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
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $entities[2]->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $amilId)
            ->getQuery()
            ->getResult();

        if (sizeof($result) !== 1) {
            return null;
        }

        return $hydrated
            ? MapHydrator::create()->hydrate($result[0])
            : $result[0];
    }
}
