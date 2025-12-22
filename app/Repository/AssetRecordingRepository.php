<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\AssetRecording;
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
class AssetRecordingRepository extends AbstractRepository
{
    use RepositoryTrait;

    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new AssetRecording());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withPage($page)
            ->withRequest($this->getRequest())
            ->paginate(new AssetRecording());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    public function getById($id): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new AssetRecording(), $id);

        if (null === $entity) {
            return null;
        }

        return $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    public function create($refId, SchemaInterface $schema): EntityInterface|array|null
    {
        $assetRecording = new AssetRecording();
        $assetRecording->setId(Uuid::v7()->toString());

        $entity = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->createReferenced(
                $refId,
                $schema,
                $assetRecording,
                new Organization()
            );

        return $entity;
    }

    public function update($id, SchemaInterface $schema): EntityInterface|array|null
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->update($id, $schema, new AssetRecording());

        return $result;
    }

    public function remove($id): EntityInterface|array|null
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->remove($id, new AssetRecording());

        return $result;
    }
}
