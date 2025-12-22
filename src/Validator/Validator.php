<?php

declare(strict_types=1);

namespace Schnell\Validator;

use ReflectionClass;
use ReflectionProperty;
use Psr\Http\Message\RequestInterface;
use Schnell\Attribute\Schema\ChainEnum;
use Schnell\Attribute\Schema\Enum;
use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Regex;
use Schnell\Attribute\Schema\Rule;
use Schnell\Attribute\Schema\TransformedClassType;
use Schnell\Exception\ValidatorException;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Validator implements ValidatorInterface
{
    /**
     * @var \Psr\Http\Message\RequestInterface|null
     */
    private ?RequestInterface $request;

    /**
     * @psalm-api
     *
     * @param \Psr\Http\Message\RequestInterface|null $request
     * @return static
     */
    public function __construct(?RequestInterface $request = null)
    {
        $this->setRequest($request);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface|null $request
     * @return \Schnell\Validator\ValidatorInterface
     */
    public static function create(?RequestInterface $request = null): ValidatorInterface
    {
        return new self($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequest(?RequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function withRequest(
        RequestInterface|null $request
    ): ValidatorInterface {
        $ret = clone $this;
        $ret->setRequest($request);
        return $ret;
    }

    /**
     * {@inheritDoc}
     */
    public function assign(SchemaInterface $schema, ?array $data = null): bool
    {
        if (null === $data && null === $this->getRequest()) {
            throw new ValidatorException("Request object is null");
        }

        /** @psalm-suppress UndefinedInterfaceMethod */
        return $this->assignBody(
            $schema,
            null !== $data ? $data : $this->getRequest()->getParsedBody()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function assignMultiple(SchemaInterface $schema): array
    {
        if (null === $this->getRequest()) {
            throw new ValidatorException("Request object is null");
        }

        $result = [];
        $lists = $this->getRequest()->getParsedBody();

        foreach ($lists as $elem) {
            if (!is_array($elem)) {
                throw new ValidatorException(
                    "each element must be an array of schema properties"
                );
            }

            /** @psalm-suppress UndefinedInterfaceMethod */
            $this->assignBody($cloned = clone $schema, $elem);
            $result[] = $cloned;
        }

        return $result;
    }

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array $parsedBody
     * @return bool
     */
    private function assignBody(SchemaInterface $schema, array $parsedBody): bool
    {
        foreach (
            $this->populateSchemaProperties($schema) as $propertyName => $attributes
        ) {
            if (
                $attributes['json'] === null ||
                $attributes['rule'] === null
            ) {
                throw new ValidatorException(
                    sprintf(
                        "in key '%s', Json and Rule method attribute must be supplied.",
                        $propertyName
                    )
                );
            }

            if (
                $attributes['rule']->getRequired() &&
                !isset($parsedBody[$attributes['json']->getName()])
            ) {
                throw new ValidatorException(
                    sprintf("'%s' is required.", $attributes['json']->getName())
                );
            }

            if (
                $attributes['rule']->getRequired() &&
                $attributes['regex'] !== null
            ) {
                $matched = preg_match(
                    $attributes['regex']->getPattern(),
                    $parsedBody[$attributes['json']->getName()]
                );

                if (false === $matched || 0 === $matched) {
                    throw new ValidatorException(
                        sprintf("'%s' is invalid.", $attributes['json']->getName())
                    );
                }
            }

            if (
                $attributes['rule']->getRequired() &&
                $attributes['enum'] !== null
            ) {
                $exists = in_array(
                    $parsedBody[$attributes['json']->getName()],
                    $attributes['enum']->getValue(),
                    true
                );

                if (false === $exists) {
                    throw new ValidatorException(
                        sprintf(
                            "'%s' is invalid. valid values are [%s]",
                            $attributes['json']->getName(),
                            join(', ', $attributes['enum']->getValue())
                        )
                    );
                }
            }

            if (
                $attributes['rule']->getRequired() &&
                $attributes['chainEnum'] !== null
            ) {
                $reflection = new ReflectionClass($schema);
                $targetField = $reflection->getProperty(
                    $attributes['chainEnum']->getField()
                );

                $scopeValue = $parsedBody[$attributes['chainEnum']->getField()];
                $chainEnumValue = $attributes['chainEnum']->getValue();

                if (false === in_array($scopeValue, array_keys($chainEnumValue), true)) {
                    throw new ValidatorException(
                        sprintf(
                            "Scope with value %d not found in chain " .
                            "enumerator ruleset.",
                            $scopeValue
                        )
                    );
                }

                $contextValueList = $chainEnumValue[$scopeValue];

                $exists = in_array(
                    $parsedBody[$attributes['json']->getName()],
                    $contextValueList,
                    true
                );

                if (false === $exists) {
                    throw new ValidatorException(
                        sprintf(
                            "Role with value %d not found in scope value %d.",
                            $parsedBody[$attributes['json']->getName()],
                            $scopeValue
                        )
                    );
                }
            }

            if ($attributes['json']->getName()[0] !== '@') {
                $fromJson = isset($parsedBody[$attributes['json']->getName()])
                    ? $parsedBody[$attributes['json']->getName()]
                    : null;
                $value = $attributes['transformedClassType'] === null
                    ? $fromJson
                    : $attributes['transformedClassType']
                        ->addArgsImmutable($fromJson)
                        ->getInstance();
            } else {
                $normalized = sprintf(
                    'get%s',
                    ucfirst(substr($attributes['json']->getName(), 1))
                );

                $this->assignBody(
                    call_user_func([$schema, $normalized]),
                    isset($parsedBody[$attributes['json']->getName()])
                        ? $parsedBody[$attributes['json']->getName()]
                        : null
                );

                $value = call_user_func([$schema, $normalized]);
            }

            call_user_func_array(
                [$schema, sprintf('set%s', $propertyName)],
                [$value]
            );
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function assignOptional(SchemaInterface $schema): bool
    {
        if (null === $this->getRequest()) {
            throw new ValidatorException("Request object is null.");
        }

        /** @psalm-suppress UndefinedInterfaceMethod */
        return $this->assignOptionalBody($schema, $this->getRequest()->getParsedBody());
    }

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array $parsedBody
     * @return bool
     */
    private function assignOptionalBody(SchemaInterface $schema, array $parsedBody): bool
    {
        foreach (
            $this->populateSchemaProperties($schema) as $propertyName => $attributes
        ) {
            if ($attributes['json'] === null) {
                throw new ValidatorException(
                    sprintf(
                        "in key '%s', Json method attribute must be supplied.",
                        $propertyName
                    )
                );
            }

            if (!isset($parsedBody[$attributes['json']->getName()])) {
                continue;
            }

            if ($attributes['regex'] !== null) {
                $matched = preg_match(
                    $attributes['regex']->getPattern(),
                    $parsedBody[$attributes['json']->getName()]
                );

                if (false === $matched || 0 === $matched) {
                    throw new ValidatorException(
                        sprintf("'%s' is invalid.", $attributes['json']->getName())
                    );
                }
            }

            if ($attributes['enum'] !== null) {
                $exists = in_array(
                    $parsedBody[$attributes['json']->getName()],
                    $attributes['enum']->getValue(),
                    true
                );

                if (false === $exists) {
                    throw new ValidatorException(
                        sprintf(
                            "'%s' is invalid. valid values are [%s]",
                            $attributes['json']->getName(),
                            join(', ', $attributes['enum']->getValue())
                        )
                    );
                }
            }

            if ($attributes['json']->getName()[0] !== '@') {
                $value = $attributes['transformedClassType'] === null
                    ? $parsedBody[$attributes['json']->getName()]
                    : $attributes['transformedClassType']
                        ->addArgsImmutable($parsedBody[$attributes['json']->getName()])
                        ->getInstance();
            } else {
                $normalized = sprintf(
                    'get%s',
                    ucfirst(substr($attributes['json']->getName(), 1))
                );

                $this->assignOptionalBody(
                    call_user_func([$schema, $normalized]),
                    $parsedBody[$attributes['json']->getName()]
                );

                $value = call_user_func([$schema, $normalized]);
            }

            call_user_func_array(
                [$schema, sprintf('set%s', $propertyName)],
                [$value]
            );
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function validateSchema(SchemaInterface $schema): void
    {
        $properties = $this->populateSchemaPropertiesWithProperty($schema);

        foreach ($properties as $name => $meta) {
            if (
                $meta['attr']['json'] === null ||
                $meta['attr']['rule'] === null
            ) {
                throw new ValidatorException(
                    sprintf(
                        "In key '%s', Json and Rule property attribute must be exist.",
                        $name
                    )
                );
            }

            if (
                $meta['attr']['rule']->getRequired() &&
                $meta['attr']['regex'] !== null
            ) {
                $matched = preg_match(
                    $meta['attr']['regex']->getPattern(),
                    strval($meta['prop']->getValue($schema))
                );

                if (false === $matched || 0 === $matched) {
                    throw new ValidatorException(
                        sprintf("'%s' is invalid.", $meta['prop']->getValue()->stringify())
                    );
                }
            }

            if (
                $meta['attr']['rule']->getRequired() &&
                $meta['attr']['enum'] !== null
            ) {
                $exists = in_array(
                    $meta['prop']->getValue($schema),
                    $meta['attr']['enum']->getValue(),
                    true
                );

                if (false === $exists) {
                    throw new ValidatorException(
                        sprintf(
                            "'%s' is invalid. Valid values are [%s]",
                            $name,
                            join(', ', $meta['attr']['enum']->getValue())
                        )
                    );
                }
            }

            if (
                $meta['attr']['rule']->getRequired() &&
                $meta['attr']['chainEnum'] !== null
            ) {
                $reflection = new ReflectionClass($schema);
                $targetField = $reflection->getProperty(
                    $meta['attr']['chainEnum']->getField()
                );

                $fieldValue = call_user_func(
                    [$schema, 'get' . ucfirst($meta['attr']['chainEnum']->getField())]
                );

                $chainEnumValue = $meta['attr']['chainEnum']->getValue();

                if (false === in_array($fieldValue, array_keys($chainEnumValue), true)) {
                    throw new ValidatorException(
                        sprintf(
                            "'%s' with value %s not found in chain " .
                            "enumerator ruleset.",
                            $meta['attr']['chainEnum']->getField(),
                            strval($fieldValue)
                        )
                    );
                }

                $contextValueList = $chainEnumValue[$fieldValue];

                $exists = in_array(
                    $meta['prop']->getValue(),
                    $contextValueList,
                    true
                );

                if (false === $exists) {
                    throw new ValidatorException(
                        sprintf(
                            "'%s' with value %s not found in '%s' value %s.",
                            $name,
                            strval($meta['prop']->getValue()),
                            $meta['attr']['chainEnum']->getField(),
                            strval($fieldValue)
                        )
                    );
                }
            }
        }

        return;
    }

    /**
     * @internal
     * @psalm-api
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     */
    private function populateSchemaProperties(SchemaInterface $schema): array
    {
        $reflection = new ReflectionClass($schema);
        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            $properties[$property->getName()] = $this->populatePropertyAttributes(
                $property
            );
        }

        return $properties;
    }

    /**
     * @internal
     * @psalm-api
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     */
    private function populateSchemaPropertiesWithProperty(SchemaInterface $schema): array
    {
        $reflection = new ReflectionClass($schema);
        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            $properties[$property->getName()] = [
                'prop' => $property,
                'attr' => $this->populatePropertyAttributes($property)
            ];
        }

        return $properties;
    }

    /**
     * @internal
     * @psalm-api
     *
     * @param \ReflectionProperty $property
     * @return array
     */
    private function populatePropertyAttributes(ReflectionProperty $property): array
    {
        $metadata = [
            'rule' => null,
            'json' => null,
            'enum' => null,
            'chainEnum' => null,
            'regex' => null,
            'transformedClassType' => null
        ];

        foreach ($property->getAttributes() as $attribute) {
            $attrInstance = $attribute->newInstance();

            if ($attrInstance instanceof Rule) {
                $metadata['rule'] = $attrInstance;
            }

            if ($attrInstance instanceof Json) {
                $metadata['json'] = $attrInstance;
            }

            if ($attrInstance instanceof Enum) {
                $metadata['enum'] = $attrInstance;
            }

            if ($attrInstance instanceof ChainEnum) {
                $metadata['chainEnum'] = $attrInstance;
            }

            if ($attrInstance instanceof Regex) {
                $metadata['regex'] = $attrInstance;
            }

            if ($attrInstance instanceof TransformedClassType) {
                $metadata['transformedClassType'] = $attrInstance;
            }
        }

        return $metadata;
    }
}
