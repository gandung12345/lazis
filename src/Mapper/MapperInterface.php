<?php

declare(strict_types=1);

namespace Schnell\Mapper;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Schnell\Entity\EntityInterface;
use Schnell\Hydrator\HydratorInterface;
use Schnell\Paginator\PageInterface;
use Schnell\Schema\SchemaInterface;

use function class_exists;

// help opcache.preload discover always-needed symbols
// @codeCoverageIgnoreStart
// phpcs:disable
class_exists(AbstractQuery::class);
// phpcs:enable
// @codeCoverageIgnoreEnd

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface MapperInterface extends RequestAwareInterface
{
    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @return void
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void;

    /**
     * @return \Doctrine\ORM\AbstractQuery|null
     */
    public function getDql(): AbstractQuery|null;

    /**
     * @param \Doctrine\ORM\AbstractQuery|null $dql
     * @return void
     */
    public function setDql(AbstractQuery|null $dql): void;

    /**
     * @psalm-api
     *
     * @param \Doctrine\ORM\AbstractQuery|null $dql
     * @return \Schnell\Mapper\MapperInterface
     */
    public function withDql(AbstractQuery|null $dql): MapperInterface;

    /**
     * @psalm-api
     *
     * @return mixed
     */
    public function runDql();

    /**
     * @return \Schnell\Hydrator\HydratorInterface|null
     */
    public function getHydrator(): ?HydratorInterface;

    /**
     * @param \Schnell\Hydrator\HydratorInterface|null $hydrator
     * @return void
     */
    public function setHydrator(?HydratorInterface $hydrator): void;

    /**
     * @psalm-api
     *
     * @param \Schnell\Hydrator\HydratorInterface|null $hydrator
     * @return \Schnell\Mapper\MapperInterface
     */
    public function withHydrator(?HydratorInterface $hydrator): MapperInterface;

    /**
     * @return \Schnell\Paginator\PageInterface|null
     */
    public function getPage(): ?PageInterface;

    /**
     * @param \Schnell\Paginator\PageInterface|null $page
     * @return void
     */
    public function setPage(?PageInterface $page): void;

    /**
     * @psalm-api
     *
     * @param \Schnell\Paginator\PageInterface|null $page
     * @return \Schnell\Mapper\MapperInterface
     */
    public function withPage(?PageInterface $page): MapperInterface;

    /**
     * @psalm-api
     *
     * @param \Schnell\Entity\EntityInterface $entity
     * @return array
     */
    public function all(EntityInterface $entity): array;

    /**
     * @psalm-api
     *
     * @param \Schnell\Entity\EntityInterface $entity
     * @return array
     */
    public function paginate(EntityInterface $entity): array;

    /**
     * @psalm-api
     *
     * @param string $parentColumn
     * @param \Schnell\Entity\EntityInterface $parent
     * @param \Schnell\Entity\EntityInterface $entity
     * @return array|null
     */
    public function paginateByParent(
        string $parentColumn,
        EntityInterface $parent,
        EntityInterface $entity
    ): ?array;

    /**
     * @psalm-api
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Schnell\Entity\EntityInterface $entity
     * @return array
     */
    public function paginateFromQueryBuilder(
        QueryBuilder $queryBuilder,
        EntityInterface $entity
    ): array;

    /**
     * @psalm-api
     *
     * @param \Schnell\Entity\EntityInterface $entity
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function find(
        EntityInterface $entity,
        $id
    ): EntityInterface|array|null;

    /**
     * @psalm-api
     *
     * @param \Schnell\Entity\EntityInterface $entity
     * @return int
     */
    public function count(EntityInterface $entity): int;

    /**
     * @psalm-api
     *
     * @param \Schnell\Entity\EntityInterface $entity
     * @param \Schnell\Entity\EntityInterface $parent
     * @return int
     */
    public function countByParent(
        EntityInterface $entity,
        EntityInterface $parent
    ): int;

    /**
     * @psalm-api
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param \Schnell\Entity\EntityInterface $entity
     * @return \Schnell\Entity\EntityInterface|array
     */
    public function create(
        SchemaInterface $schema,
        EntityInterface $entity
    ): EntityInterface|array;

    /**
     * @psalm-api
     *
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param \Schnell\Entity\EntityInterface $targetEntity
     * @param \Schnell\Entity\EntityInterface $referencedEntity
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function createReferenced(
        $refId,
        SchemaInterface $schema,
        EntityInterface $targetEntity,
        EntityInterface $referencedEntity
    ): EntityInterface|array|null;

    /**
     * @psalm-api
     *
     * @param mixed $id
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param \Schnell\Entity\EntityInterface $entity
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function update(
        $id,
        SchemaInterface $schema,
        EntityInterface $entity
    ): EntityInterface|array|null;

    /**
     * @psalm-api
     *
     * @param mixed $id
     * @param \Schnell\Entity\EntityInterface $entity
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function remove($id, EntityInterface $entity): EntityInterface|array|null;
}
