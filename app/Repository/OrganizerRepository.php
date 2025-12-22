<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Throwable;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Organizer;
use Lazis\Api\Entity\OrganizerBulkResponseState;
use Schnell\Entity\EntityInterface;
use Schnell\Http\Code as HttpCode;
use Schnell\Hydrator\ArrayHydrator;
use Schnell\Hydrator\MapHydrator;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class OrganizerRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new Organizer());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new Organizer());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @param bool $hydrated
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id, bool $hydrated = true): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new Organizer(), $id);

        if (null === $entity) {
            return null;
        }

        return false === $hydrated
            ? $entity
            : $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param bool $hydrated
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function create(
        $refId,
        SchemaInterface $schema,
        bool $hydrated = true
    ): EntityInterface|array|null {
        $organizer = new Organizer();
        $organizer->setId(Uuid::v7()->toString());

        $entity = $this->getMapper()
            ->withHydrator($hydrated ? new MapHydrator() : null)
            ->createReferenced(
                $refId,
                $schema,
                $organizer,
                new Organization()
            );

        return $entity;
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Entity\EntityInterface $parent
     * @param array $schemas
     * @param bool $hydrated
     * @return array
     */
    public function createBulk(
        $refId,
        EntityInterface $parent,
        array $schemas,
        bool $hydrated = false
    ): array {
        $result = [];

        foreach ($schemas as $key => $schema) {
            try {
                $organizer = $this->create($refId, $schema, false);
                $result[] = $this->createOrganizerStateObject(
                    $key,
                    $organizer->getId(),
                    HttpCode::CREATED,
                    sprintf(
                        'Organizer created with id \'%s\'.',
                        $organizer->getId()
                    )
                );
            } catch (Throwable $e) {
                $result[] = $this->createOrganizerStateObject(
                    $key,
                    '',
                    HttpCode::UNPROCESSABLE_ENTITY,
                    $e->getMessage()
                );
            }
        }

        return false === $hydrated
            ? $result
            : ArrayHydrator::create()->hydrate($result);
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
            ->update($id, $schema, new Organizer());

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
            ->remove($id, new Organizer());

        return $result;
    }

    /**
     * @param int $index
     * @param string $id
     * @param int $status
     * @param string $message
     * @return \Schnell\Entity\EntityInterface
     */
    public function createOrganizerStateObject(
        int $index,
        string $id,
        int $status,
        string $message
    ): EntityInterface {
        $state = new OrganizerBulkResponseState();

        $state->setIndex($index);
        $state->setId($id);
        $state->setStatus($status);
        $state->setReason(HttpCode::toString($status));
        $state->setMessage($message);

        return $state;
    }
}
