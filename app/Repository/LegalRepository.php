<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Lazis\Api\Entity\Legal;
use Lazis\Api\Entity\Organization;
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
class LegalRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new Legal());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new Legal());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @return Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new Legal(), $id);

        if (null === $entity) {
            return null;
        }

        return $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    /**
     * @param mixed $refId
     * @param Schnell\Schema\SchemaInterface $schema
     * @return Schnell\Entity\EntityInterface|array|null
     */
    public function create($refId, SchemaInterface $schema): EntityInterface|array|null
    {
        $legal = new Legal();
        $legal->setId(Uuid::v7()->toString());

        $entity = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->createReferenced(
                $refId,
                $schema,
                $legal,
                new Organization()
            );

        return $entity;
    }

    /**
     * @param mixed $id
     * @param Schnell\Schema\SchemaInterface $schema
     * @return Schnell\Entity\EntityInterface|array|null
     */
    public function update($id, SchemaInterface $schema): EntityInterface|array|null
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->update($id, $schema, new Legal());

        return $result;
    }

    /**
     * @param mixed $id
     * @return Schnell\Entity\EntityInterface|array|null
     */
    public function remove($id): EntityInterface|array|null
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->remove($id, new Legal());

        return $result;
    }
}
