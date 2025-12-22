<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Throwable;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\OrganizationBulkResponseState;
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
class OrganizationRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new Organization());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new Organization());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @param bool $hydrated
     * @return Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id, bool $hydrated = false): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new Organization(), $id);

        if (null === $entity) {
            return null;
        }

        return false === $hydrated
            ? $entity
            : $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    /**
     * @param Schnell\Schema\SchemaInterface $schema
     * @param bool $hydrated
     * @return Schnell\Entity\EntityInterface|array
     */
    public function create(SchemaInterface $schema, bool $hydrated = true): EntityInterface|array
    {
        $organization = new Organization();
        $organization->setId(Uuid::v7()->toString());

        $entity = $this->getMapper()
            ->withHydrator($hydrated ? new MapHydrator() : null)
            ->create($schema, $organization);

        return $entity;
    }

    /**
     * @param array $schemas
     * @param bool $hydrated
     * @return array
     */
    public function createBulk(array $schemas, bool $hydrated = false): array
    {
        $result = [];

        foreach ($schemas as $key => $schema) {
            try {
                $organization = $this->create($schema, false);
                $result[] = $this->createOrganizationStateObject(
                    $key,
                    $organization->getId(),
                    HttpCode::CREATED,
                    sprintf('Organization created with id \'%s\'.', $organization->getId())
                );
            } catch (Throwable $e) {
                $result[] = $this->createOrganizationStateObject(
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
     * @param Schnell\Schema\SchemaInterface $schema
     * @return Schnell\Schema\SchemaInterface|array|null
     */
    public function update($id, SchemaInterface $schema): EntityInterface|array|null
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->update($id, $schema, new Organization());

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
            ->remove($id, new Organization());

        return $result;
    }

    /**
     * @param int $index
     * @param string $id
     * @param int $status
     * @param string $message
     * @return \Schnell\Entity\EntityInterface
     */
    private function createOrganizationStateObject(
        int $index,
        string $id,
        int $status,
        string $message
    ): EntityInterface {
        $state = new OrganizationBulkResponseState();

        $state->setIndex($index);
        $state->setId($id);
        $state->setStatus($status);
        $state->setReason(HttpCode::toString($status));
        $state->setMessage($message);

        return $state;
    }
}
