<?php

declare(strict_types=1);

namespace Schnell\Mapper;

use ReflectionClass;
use ReflectionProperty;
use Throwable;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Query\Expr;
use Psr\Http\Message\RequestInterface;
use Schnell\Attribute\Entity\GeneratedError;
use Schnell\Entity\EntityInterface;
use Schnell\Http\FQL\InterceptorFactory;
use Schnell\Hydrator\HydratorInterface;
use Schnell\Paginator\PageInterface;
use Schnell\Schema\SchemaInterface;

use function array_filter;
use function array_map;
use function call_user_func;
use function call_user_func_array;
use function class_exists;
use function get_class;
use function ltrim;
use function preg_match;
use function rtrim;
use function str_replace;

// help opcache.preload discover always-needed symbols
// @codeCoverageIgnoreStart
// phpcs:disable
class_exists(ReflectionClass::class);
class_exists(AbstractQuery::class);
// phpcs:enable
// @codeCoverageIgnoreEnd

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Mapper implements MapperInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var \Doctrine\ORM\AbstractQuery|null
     */
    private ?AbstractQuery $dql = null;

    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var \Schnell\Hydrator\HydratorInterface|null
     */
    private ?HydratorInterface $hydrator = null;

    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var \Schnell\Paginator\PageInterface|null
     */
    private ?PageInterface $page = null;

    /**
     * @var \Psr\Http\Message\RequestInterface|null
     */
    private ?RequestInterface $request = null;

    /**
     * @psalm-api
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @return static
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->setEntityManager($entityManager);
        $this->setDql(null);
    }

    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @return void
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getDql(): AbstractQuery|null
    {
        return $this->dql;
    }

    /**
     * {@inheritdoc}
     */
    public function setDql(AbstractQuery|null $dql): void
    {
        $this->dql = $dql;
    }

    /**
     * {@inheritdoc}
     */
    public function withDql(AbstractQuery|null $dql): MapperInterface
    {
        $ret = clone $this;
        $ret->setDql($dql);
        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function runDql()
    {
        /** @psalm-suppress PossiblyNullReference */
        return $this->getDql()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getHydrator(): ?HydratorInterface
    {
        return $this->hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function setHydrator(?HydratorInterface $hydrator): void
    {
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function withHydrator(?HydratorInterface $hydrator): MapperInterface
    {
        $ret = clone $this;
        $ret->setHydrator($hydrator);
        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function getPage(): ?PageInterface
    {
        return $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function setPage(?PageInterface $page): void
    {
        $this->page = $page;
    }

    /**
     * {@inheritdoc}
     */
    public function withPage(?PageInterface $page): MapperInterface
    {
        $ret = clone $this;
        $ret->setPage($page);
        return $ret;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequest(): ?Requestinterface
    {
        return $this->request;
    }

    /**
     * {@inheritDoc}
     */
    public function setRequest(?RequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * {@inheritDoc}
     */
    public function withRequest(?RequestInterface $request): MapperInterface
    {
        $that = clone $this;
        $that->setRequest($request);
        return $that;
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress UnusedParam
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return array
     */
    private function getResultFromQueryBuilder(QueryBuilder $queryBuilder): array
    {
        $queryBuilder = null === $this->getPage()
            ? $queryBuilder
            : $queryBuilder
                ->setFirstResult($this->getPage()->getOffset())
                ->setMaxResults($this->getPage()->getPerPage());

        $cacheableQuery = $queryBuilder->getQuery();

        $result = null === $this->getHydrator()
            ? $cacheableQuery->getResult()
            : $this->getHydrator()->hydrate(
                $cacheableQuery->getResult()
            );

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function all(EntityInterface $entity): array
    {
        $hydrator = $this->getHydrator();
        $result = $this->getEntityManager()
            ->getRepository(get_class($entity))
            ->findAll();

        return $hydrator === null
            ? $result
            : $hydrator->hydrate($result);
    }

    /**
     * @internal
     *
     * @param string $parentColumn
     * @param \Schnell\Entity\EntityInterface $parent
     * @param \Schnell\Entity\EntityInterface $entity
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getDqlWithParent(
        string $parentColumn,
        EntityInterface $parent,
        EntityInterface $entity,
    ): QueryBuilder {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder = $queryBuilder
            ->select($entity->getQueryBuilderAlias())
            ->from($parent->getDqlName(), $parent->getQueryBuilderAlias())
            ->join(
                $entity->getDqlName(),
                $entity->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.%s',
                    $parent->getQueryBuilderAlias(),
                    $entity->getQueryBuilderAlias(),
                    $parentColumn
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $parent->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $parent->getId());

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(EntityInterface $entity): array
    {
        $queryBuilder = $this->getEntityManager()
            ->getRepository(get_class($entity))
            ->createQueryBuilder($entity->getQueryBuilderAlias());

        return $this->paginateFromQueryBuilder($queryBuilder, $entity);
    }

    /**
     * {@inheritDoc}
     */
    public function paginateByParent(
        string $parentColumn,
        EntityInterface $parent,
        EntityInterface $entity
    ): ?array {
        return $this->paginateFromQueryBuilder(
            $this->getDqlWithParent($parentColumn, $parent, $entity),
            $entity
        );
    }

    /**
     * {@inheritDoc}
     */
    public function paginateFromQueryBuilder(
        QueryBuilder $queryBuilder,
        EntityInterface $entity
    ): array {
        if ($this->getRequest() === null) {
            return $this->getResultFromQueryBuilder($queryBuilder);
        }

        $interceptorFactory = new InterceptorFactory(
            $this->getRequest(),
            $queryBuilder,
            $entity
        );

        $interceptor = $interceptorFactory->createInterceptor();

        if (null === $interceptor) {
            return $this->getResultFromQueryBuilder($queryBuilder);
        }

        return $this->getResultFromQueryBuilder($interceptor->intercept());
    }

    /**
     * {@inheritDoc}
     */
    public function find(
        EntityInterface $entity,
        $id
    ): EntityInterface|array|null {
        try {
            $result = $this->getEntityManager()
                ->getRepository(get_class($entity))
                ->find($id);
        } catch (Throwable $e) {
            $result = null;
        }

        if (null === $result) {
            return null;
        }

        /** @psalm-suppress LessSpecificReturnStatement */
        return $this->getHydrator() === null
            ? $result
            : $this->getHydrator()->hydrate($result);
    }

    /**
     * {@inheritDoc}
     */
    public function count(EntityInterface $entity): int
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager
            ->createQueryBuilder()
            ->select(
                sprintf(
                    'count(%s)',
                    $entity->getQueryBuilderAlias()
                )
            )
            ->from($entity->getDqlName(), $entity->getQueryBuilderAlias());

        if (null === $this->getRequest()) {
            return $queryBuilder
                ->getQuery()
                ->getSingleScalarResult();
        }

        $interceptorFactory = new InterceptorFactory(
            $this->getRequest(),
            $queryBuilder,
            $entity
        );

        $interceptor = $interceptorFactory->createInterceptor();

        if (null === $interceptor) {
            return $queryBuilder
                ->getQuery()
                ->getSingleScalarResult();
        }

        return $interceptor->intercept()
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritDoc}
     */
    public function countByParent(
        EntityInterface $entity,
        EntityInterface $parent
    ): int {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $resultCount = $queryBuilder
            ->select(
                sprintf(
                    'count(%s)',
                    $entity->getQueryBuilderAlias()
                )
            )
            ->from($parent->getDqlName(), $parent->getQueryBuilderAlias())
            ->join(
                $entity->getDqlName(),
                $entity->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.%s',
                    $parent->getQueryBuilderAlias(),
                    $entity->getQueryBuilderAlias(),
                    ltrim(rtrim($parent->getQueryBuilderAlias(), '_'), '_')
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $parent->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $parent->getId())
            ->getQuery()
            ->getSingleScalarResult();

        return $resultCount;
    }

    /**
     * {@inheritdoc}
     */
    public function create(
        SchemaInterface $schema,
        EntityInterface $entity
    ): EntityInterface|array {
        $methods = $this->getMethodNamesFromClassObject($entity, true);
        $clonedEntity = clone $entity;

        foreach ($methods as $method) {
            call_user_func_array(
                [$clonedEntity, $method],
                [call_user_func([$schema, str_replace('set', 'get', $method)])]
            );
        }

        $hydrator = $this->getHydrator();

        try {
            $this->getEntityManager()->persist($clonedEntity);
            $this->getEntityManager()->flush();
        } catch (ConstraintViolationException | UniqueConstraintViolationException $e) {
            $result = $this->handleDoctrineException(
                $clonedEntity,
                $e
            );

            throw $result === null ? $e : $result;
        }

        return $hydrator === null
            ? $clonedEntity
            : $hydrator->hydrate($clonedEntity);
    }

    /**
     * {@inheritdoc}
     */
    public function createReferenced(
        $refId,
        SchemaInterface $schema,
        EntityInterface $targetEntity,
        EntityInterface $referencedEntity,
    ): EntityInterface|array|null {
        $refFqcn = get_class($referencedEntity);
        $methods = $this->getMethodNamesFromClassObject($targetEntity, true);

        try {
            $referencedEntity = $this->getEntityManager()
                ->getRepository($refFqcn)
                ->find($refId);
        } catch (Throwable $e) {
            $referencedEntity = null;
        }

        if (null === $referencedEntity) {
            return null;
        }

        $splittedFqcn = preg_split('/\\\/', $refFqcn);

        /** @psalm-suppress PossiblyFalseArgument */
        $refClassName = end($splittedFqcn);
        $clonedTargetEntity = clone $targetEntity;

        call_user_func_array(
            [$clonedTargetEntity, sprintf('set%s', $refClassName)],
            [$referencedEntity]
        );

        foreach ($methods as $method) {
            call_user_func_array(
                [$clonedTargetEntity, $method],
                [call_user_func([$schema, str_replace('set', 'get', $method)])]
            );
        }

        $hydrator = $this->getHydrator();

        try {
            $this->getEntityManager()->persist($clonedTargetEntity);
            $this->getEntityManager()->flush();
        } catch (ConstraintViolationException | UniqueConstraintViolationException $e) {
            $result = $this->handleDoctrineException(
                $clonedTargetEntity,
                $e
            );

            throw $result === null ? $e : $result;
        }

        return null === $hydrator
            ? $clonedTargetEntity
            : $hydrator->hydrate($clonedTargetEntity);
    }

    /**
     * {@inheritdoc}
     */
    public function update(
        $id,
        SchemaInterface $schema,
        EntityInterface $entity
    ): EntityInterface|array|null {
        try {
            $entity = $this->getEntityManager()
                ->getRepository(get_class($entity))
                ->find($id);
        } catch (Throwable $e) {
            $entity = null;
        }

        if (null === $entity) {
            return null;
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        $methods = $this->getMethodNamesFromClassObject($entity);

        foreach ($methods as $method) {
            $value = call_user_func([$schema, $method]);

            call_user_func_array(
                [$entity, preg_replace('/^(?:get)/', '${1}set', $method)],
                [null === $value
                    ? call_user_func([$entity, $method])
                    : $value]
            );
        }

        $this->getEntityManager()->flush();

        /** @psalm-suppress LessSpecificReturnStatement */
        return null === $this->getHydrator()
            ? $entity
            : $this->getHydrator()->hydrate($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id, EntityInterface $entity): EntityInterface|array|null
    {
        try {
            $entity = $this->getEntityManager()
                ->getRepository(get_class($entity))
                ->find($id);
        } catch (Throwable $e) {
            $entity = null;
        }

        if ($entity === null) {
            return null;
        }

        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();

        /** @psalm-suppress LessSpecificReturnStatement */
        return null === $this->getHydrator()
            ? $entity
            : $this->getHydrator()->hydrate($entity);
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress PossiblyUnusedParam
     *
     * @param \Schnell\Entity\EntityInterface $entity
     * @param bool $mutator
     * @return array
     */
    private function getMethodNamesFromClassObject(
        EntityInterface $entity,
        bool $mutator = false
    ): array {
        $reflection = new ReflectionClass($entity);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);
        $methods = [];

        foreach ($properties as $property) {
            if (
                $this->isPrimaryKey($property) ||
                $this->isRelational($property)
            ) {
                continue;
            }

            if (
                $property->getName() === 'createdAt' ||
                $property->getName() === 'updatedAt'
            ) {
                continue;
            }

            $methods[] = sprintf(
                '%s%s',
                $mutator ? 'set' : 'get',
                ucfirst($property->getName())
            );
        }

        return $methods;
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress UnusedParam
     *
     * @param \ReflectionProperty $property
     * @return bool
     */
    private function isPrimaryKey(ReflectionProperty $property): bool
    {
        $attributes = $property->getAttributes();

        foreach ($attributes as $attribute) {
            if ($attribute->getName() === Id::class) {
                return true;
            }
        }

        return false;
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress PossiblyUnusedParam
     * @psalm-suppress UnusedParam
     *
     * @param \ReflectionProperty $property
     * @return bool
     */
    private function isRelational(ReflectionProperty $property): bool
    {
        $attributes = $property->getAttributes();

        foreach ($attributes as $attribute) {
            if (
                $attribute->getName() === OneToOne::class ||
                $attribute->getName() === OneToMany::class ||
                $attribute->getName() === ManyToOne::class ||
                $attribute->getName() === JoinColumn::class
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress UnusedParam
     *
     * @param \Schnell\Entity\EntityInterface $entity
     * @param \Throwable $previous
     * @return \Throwable|null
     */
    private function handleDoctrineException(
        EntityInterface $entity,
        Throwable $previous
    ): ?Throwable {
        $reflection = new ReflectionClass($entity);
        $attributes = $reflection->getAttributes();
        $generated = null;

        foreach ($attributes as $attribute) {
            if ($attribute->getName() !== GeneratedError::class) {
                continue;
            }

            $generated = $attribute->newInstance();
            $generated->setEntity($entity);

            /**
             * @psalm-suppress PossiblyNullReference
             * @psalm-suppress UndefinedInterfaceMethod
             */
            $generated->setSqlState($previous->getPrevious()->getSqlState());
        }

        return $generated === null
            ? null
            : $generated->generateException();
    }
}
