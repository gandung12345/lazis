<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Lazis\Api\Entity\NuCoinCrossTransactionRecord;
use Schnell\Entity\EntityInterface;
use Schnell\Hydrator\MapHydrator;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinCrossTransactionRecordRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new NuCoinCrossTransactionRecord());

        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new NuCoinCrossTransactionRecord());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id): EntityInterface|array|null
    {
        $entity = $this->getMapper()
            ->find(new NuCoinCrossTransactionRecord(), $id);

        if (null === $entity) {
            return null;
        }

        return $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param bool $hydrated
     * @return \Schnell\Entity\EntityInterface|array
     */
    public function create(SchemaInterface $schema, bool $hydrated = true): EntityInterface|array
    {
        $nuCoinCrossTransactionRecord = new NuCoinCrossTransactionRecord();
        $nuCoinCrossTransactionRecord->setId(Uuid::v7()->toString());

        $entity = $this->getMapper()
            ->withHydrator($hydrated ? new MapHydrator() : null)
            ->create($schema, $nuCoinCrossTransactionRecord);

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
            ->update($id, $schema, new NuCoinCrossTransactionRecord());

        return $result;
    }
}
