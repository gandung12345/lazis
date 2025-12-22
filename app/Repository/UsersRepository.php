<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Lazis\Api\Entity\Organizer;
use Lazis\Api\Entity\Users;
use Schnell\Entity\EntityInterface;
use Schnell\Hydrator\MapHydrator;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;
use Psr\Http\Message\ServerRequestInterface;

use function password_hash;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class UsersRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new Users());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withPage($page)
            ->withRequest($this->getRequest())
            ->paginate(new Users());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new Users(), $id);

        if (null === $entity) {
            return null;
        }

        return $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|null|array
     */
    public function create($refId, SchemaInterface $schema): EntityInterface|null|array
    {
        $users = new Users();
        $users->setId(Uuid::v7()->toString());

        $schema->setPassword(password_hash($schema->getPassword(), PASSWORD_DEFAULT));

        $entity = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->createReferenced(
                $refId,
                $schema,
                $users,
                new Organizer()
            );

        return $entity;
    }

    /**
     * @param mixed $id
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|null|array
     */
    public function update($id, SchemaInterface $schema): EntityInterface|null|array
    {
        if (null !== $schema->getPassword()) {
            $schema->setPassword(
                password_hash($schema->getPassword(), PASSWORD_DEFAULT)
            );
        }

        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->update($id, $schema, new Users());

        return $result;
    }
}
