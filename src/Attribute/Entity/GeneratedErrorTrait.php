<?php

declare(strict_types=1);

namespace Schnell\Attribute\Entity;

use Throwable;
use ReflectionClass;
use ReflectionProperty;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Proxy\InternalProxy;
use Schnell\Exception\MapperException;
use Schnell\Mapper\Query\Error as QueryError;

use function get_parent_class;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
trait GeneratedErrorTrait
{
    /**
     * @var string
     */
    public const string ONE_TO_ONE = 'one-to-one';

    /**
     * @var string
     */
    public const string ONE_TO_MANY = 'one-to-many';

    /**
     * @var string
     */
    public const string MANY_TO_ONE = 'many-to-one';

    /**
     * @var string
     */
    public const string MANY_TO_MANY = 'many-to-many';

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress PossiblyNullReference
     *
     * @return \Throwable|null
     */
    private function handleIntegrityConstraintViolation(): ?Throwable
    {
        if (
            $this->getTargetColumn() === null ||
            $this->getEntity() === null
        ) {
            return null;
        }

        $attributes = $this->getColumnAttributeInstances();

        if (
            !$this->hasRelational($attributes) &&
            !$this->isPrimaryKey($attributes)
        ) {
            return $this->handleIntegrityUniqueConstraintViolation();
        }

        $relationType = $this->fetchRelationType($attributes);

        $message = sprintf(
            '(%s): %s on table %s (id: %s) cannot be duplicated.',
            $relationType ?? 'null',
            $this->isPrimaryKey($attributes)
                ? 'primary key'
                : 'foreign key',
            $this->getEntity()->getCanonicalTableName(),
            $this->isPrimaryKey($attributes)
                ? $this->fetchPrimaryKey()
                : $this->fetchForeignKey()
        );

        $queryError = new QueryError($this->getSqlState());
        $exception = new MapperException($message);
        $exception->setSqlState($this->getSqlState());
        $exception->setSqlStateDescription($queryError->getErrorClassification());

        return $exception;
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress PossiblyNullReference
     *
     * @return \Throwable|null
     */
    private function handleIntegrityUniqueConstraintViolation(): ?Throwable
    {
        if (
            $this->getTargetColumn() === null ||
            $this->getEntity() === null
        ) {
            return null;
        }

        $attributes = $this->getColumnAttributeInstances();

        if (
            $this->hasRelational($attributes) &&
            $this->isPrimaryKey($attributes)
        ) {
            return null;
        }

        $message = sprintf(
            "Column %s on table %s with value '%s' is unique. " .
            "Cannot be duplicated.",
            $this->getTargetColumn(),
            $this->getEntity()->getCanonicalTableName(),
            call_user_func(
                [$this->getEntity(), 'get' . ucfirst($this->getTargetColumn())]
            )
        );

        $queryError = new QueryError($this->getSqlState());
        $exception = new MapperException($message);
        $exception->setSqlState($this->getSqlState());
        $exception->setSqlStateDescription($queryError->getErrorClassification());

        return $exception;
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress PossiblyNullArgument
     *
     * @return array
     */
    private function getColumnAttributeInstances(): array
    {
        $result = [];
        $reflection = new ReflectionClass($this->getEntity());
        $properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);

        foreach ($properties as $property) {
            if ($property->getName() !== $this->getTargetColumn()) {
                continue;
            }

            $attributes = $property->getAttributes();

            foreach ($attributes as $attribute) {
                $result[] = $attribute->newInstance();
            }
        }

        return $result;
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress UnusedParam
     * @psalm-suppress PossiblyUnusedParam
     *
     * @param array $attributes
     * @return bool
     */
    private function hasRelational(array $attributes): bool
    {
        foreach ($attributes as $attribute) {
            if (
                is_a($attribute, OneToOne::class) ||
                is_a($attribute, OneToMany::class) ||
                is_a($attribute, ManyToOne::class) ||
                is_a($attribute, ManyToMany::class)
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
     * @param array $attributes
     * @return bool
     */
    private function isPrimaryKey(array $attributes): bool
    {
        foreach ($attributes as $attribute) {
            if (is_a($attribute, Id::class)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress UnusedParam
     * @psalm-suppress PossiblyUnusedParam
     *
     * @param array $attributes
     * @return string|null
     */
    private function fetchRelationType(array $attributes): ?string
    {
        $target = null;

        foreach ($attributes as $attribute) {
            if (
                !is_a($attribute, OneToOne::class) &&
                !is_a($attribute, OneToMany::class) &&
                !is_a($attribute, ManyToOne::class) &&
                !is_a($attribute, ManyToMany::class)
            ) {
                continue;
            }

            $target = $attribute;
        }

        if (null === $target) {
            return null;
        }

        $targetClass = get_class($target);

        switch ($targetClass) {
            case OneToOne::class:
                return self::ONE_TO_ONE;
            case OneToMany::class:
                return self::ONE_TO_MANY;
            case ManyToOne::class:
                return self::MANY_TO_ONE;
            case ManyToMany::class:
                return self::MANY_TO_MANY;
        }

        return null;
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress PossiblyNullArgument
     *
     * @return mixed
     */
    private function fetchPrimaryKey()
    {
        if (null === $this->getTargetColumn()) {
            return null;
        }

        return call_user_func([
            $this->getEntity(),
            sprintf('get%s', ucfirst($this->getTargetColumn()))
        ]);
    }

    /**
     * @internal
     * @psalm-api
     * @psalm-suppress PossiblyNullArgument
     *
     * @return mixed
     */
    private function fetchForeignKey()
    {
        if (null === $this->getTargetColumn()) {
            return null;
        }

        $entity = call_user_func([
            $this->getEntity(),
            sprintf('get%s', ucfirst($this->getTargetColumn()))
        ]);

        if (null === $entity) {
            return null;
        }

        $reflection = $entity instanceof InternalProxy
            ? new ReflectionClass(get_parent_class($entity))
            : new ReflectionClass($entity);

        $properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);

        foreach ($properties as $property) {
            $exists = in_array(
                Id::class,
                array_map(fn($attr) => $attr->getName(), $property->getAttributes()),
                true
            );

            if (false === $exists) {
                continue;
            }

            return call_user_func([
                $entity,
                sprintf('get%s', ucfirst($property->getName()))
            ]);
        }

        return null;
    }
}
