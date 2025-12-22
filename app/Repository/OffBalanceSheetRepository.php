<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Lazis\Api\Entity\Mosque;
use Lazis\Api\Entity\OffBalanceSheet;
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
class OffBalanceSheetRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new OffBalanceSheet());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new OffBalanceSheet());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @param bool $hydrated
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id, bool $hydrated = false): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new OffBalanceSheet(), $id);

        if (null === $entity) {
            return null;
        }

        return false === $hydrated
            ? $entity
            : $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function create($refId, SchemaInterface $schema): EntityInterface|array|null
    {
        $offBalanceSheet = new OffBalanceSheet();
        $offBalanceSheet->setId(Uuid::v7()->toString());

        $entity = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->createReferenced(
                $refId,
                $schema,
                $offBalanceSheet,
                new Mosque()
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
            ->update($id, $schema, new OffBalanceSheet());

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
            ->remove($id, new OffBalanceSheet());

        return $result;
    }
}
