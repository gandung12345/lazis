<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Throwable;
use Lazis\Api\Entity\Donee;
use Lazis\Api\Entity\DoneeBulkResponseState;
use Lazis\Api\Entity\Organization;
use Schnell\Entity\EntityInterface;
use Schnell\Hydrator\ArrayHydrator;
use Schnell\Hydrator\MapHydrator;
use Schnell\Http\Code as HttpCode;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DoneeRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new Donee());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new Donee());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @return \Schnell\Entity\EntityInterface|null|array
     */
    public function getById($id): EntityInterface|null|array
    {
        $entity = $this->getMapper()->find(new Donee(), $id);

        if (null === $entity) {
            return null;
        }

        return $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param bool $hydrated
     * @return \Schnell\Entity\EntityInterface|null|array
     */
    public function create(
        $refId,
        SchemaInterface $schema,
        bool $hydrated = true
    ): EntityInterface|null|array {
        $donee = new Donee();
        $donee->setId(Uuid::v7()->toString());

        $entity = $this->getMapper()
            ->withHydrator($hydrated ? new MapHydrator() : null)
            ->createReferenced(
                $refId,
                $schema,
                $donee,
                new Organization()
            );

        return $entity;
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Entity\EntityInterface $parent,
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
                $donee = $this->create($refId, $schema, false);
                $result[] = $this->createDoneeStateObject(
                    $key,
                    $donee->getId(),
                    HttpCode::CREATED,
                    sprintf('Donee created with id \'%s\'.', $donee->getId())
                );
            } catch (Throwable $e) {
                $result[] = $this->createDoneeStateObject(
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
     * @return \Schnell\Entity\EntityInterface|null|array
     */
    public function update($id, SchemaInterface $schema): EntityInterface|null|array
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->update($id, $schema, new Donee());

        return $result;
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|null|array
     */
    public function remove($id): EntityInterface|null|array
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->remove($id, new Donee());

        return $result;
    }

    /**
     * @param int $index
     * @param string $id
     * @param int $status
     * @param string $message
     * @return \Schnell\Entity\EntityInterface
     */
    private function createDoneeStateObject(
        int $index,
        string $id,
        int $status,
        string $message
    ): EntityInterface {
        $state = new DoneeBulkResponseState();

        $state->setIndex($index);
        $state->setId($id);
        $state->setStatus($status);
        $state->setReason(HttpCode::toString($status));
        $state->setMessage($message);

        return $state;
    }
}
