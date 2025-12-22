<?php

namespace Schnell\Hydrator;

use DateTime;
use ReflectionClass;
use ReflectionProperty;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Schnell\Attribute\AttributeInterface;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\EntityInterface;
use Schnell\Exception\HydratorException;

use function class_exists;
use function is_array;
use function is_object;
use function sprintf;
use function ucfirst;

// help opcache.preload discover always-needed symbols
// phpcs:disable
class_exists(ReflectionClass::class);
class_exists(ReflectionProperty::class);
// phpcs:enable

/**
 * @psalm-api
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ArrayHydrator implements HydratorInterface
{
    /**
     * @return \Schnell\Hydrator\HydratorInterface
     */
    public static function create(): HydratorInterface
    {
        return new ArrayHydrator();
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($value)
    {
        $ret = [];

        foreach ($value as $data) {
            $ret[] = $this->hydrateSingle($data);
        }

        return $ret;
    }

    /**
     * @param \Schnell\Entity\EntityInterface $entity
     * @return array
     * @throws \Schnell\Exception\HydratorException
     */
    private function hydrateSingle(EntityInterface $entity): array
    {
        $hydrated = [];
        $reflection = new ReflectionClass($entity);
        $fields = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);

        foreach ($fields as $field) {
            if (
                $this->isRelational($field) ||
                $field->getName() === 'lazyObjectState'
            ) {
                continue;
            }

            if (($jsonAttr = $this->getJsonAttribute($field)) === null) {
                throw new HydratorException(
                    sprintf(
                        'Column %s has no json attribute.',
                        $field->getName()
                    )
                );
            }

            $value = call_user_func(
                [$entity, sprintf('get%s', ucfirst($field->getName()))]
            );

            if (is_array($value)) {
                $hydrated[$jsonAttr->getName()] = $this->hydrate($value);
                continue;
            }

            /** @psalm-suppress UndefinedInterfaceMethod */
            $hydrated[$jsonAttr->getName()] = is_object($value)
                ? $this->hydrateObject($value)
                : $value;
        }

        return $hydrated;
    }

    /**
     * @internal
     * @psalm-api
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
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     *
     * @param \ReflectionProperty $property
     * @return \Schnell\Attribute\AttributeInterface|null
     */
    private function getJsonAttribute(ReflectionProperty $property): ?AttributeInterface
    {
        $result = null;
        $attributes = $property->getAttributes();

        foreach ($attributes as $attribute) {
            if ($attribute->getName() !== Json::class) {
                continue;
            }

            $result = $attribute->newInstance();
        }

        return $result;
    }

    /**
     * @internal
     *
     * @param object $object
     * @return mixed
     */
    private function hydrateObject(object $object): mixed
    {
        if ($object instanceof StringifiedDecoratorInterface) {
            return strval($object);
        }

        if ($object instanceof EntityInterface) {
            return $this->hydrateSingle($object);
        }

        if ($object instanceof DateTime) {
            return $object->format('Y-m-d H:i:s');
        }

        return null;
    }
}
