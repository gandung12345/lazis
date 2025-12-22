<?php

declare(strict_types=1);

namespace Schnell\Hydrator;

use DateTime;
use ReflectionClass;
use ReflectionProperty;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Proxy\InternalProxy;
use Schnell\Attribute\AttributeInterface;
use Schnell\Attribute\Schema\Json;
use Schnell\Decorator\StringifiedDecoratorInterface;
use Schnell\Entity\EntityInterface;
use Schnell\Exception\HydratorException;

use function is_a;

/**
 * @psalm-api
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class MapHydrator implements HydratorInterface
{
    use HydratorTrait;

    /**
     * @return \Schnell\Hydrator\HydratorInterface
     */
    public static function create(): HydratorInterface
    {
        return new MapHydrator();
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($value)
    {
        if (false === is_a($value, EntityInterface::class)) {
            throw new HydratorException(
                sprintf(
                    "The supplied value is not an instance of '%s'.",
                    EntityInterface::class
                )
            );
        }

        $hydrated = [];
        $reflection = new ReflectionClass($value);
        $fields = $value instanceof InternalProxy
            ? $this->getProxyClassProperties($value)
            : $reflection->getProperties(ReflectionProperty::IS_PRIVATE);

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

            $entity = call_user_func([
                $value,
                sprintf('get%s', ucfirst($field->getName()))
            ]);

            /** @psalm-suppress UndefinedInterfaceMethod */
            $hydrated[$jsonAttr->getName()] = is_object($entity)
                ? $this->hydrateObject($entity)
                : (
                    is_array($entity)
                        ? ArrayHydrator::create()->hydrate($entity)
                        : $entity
                  );
        }

        return $hydrated;
    }

    /**
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
     *
     * @psalm-api
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
            return $this->hydrate($object);
        }

        if ($object instanceof DateTime) {
            return $object->format('Y-m-d H:i:s');
        }

        return null;
    }
}
