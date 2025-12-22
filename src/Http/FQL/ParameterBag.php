<?php

declare(strict_types=1);

namespace Schnell\Http\FQL;

use ArrayIterator;

/**
 * @psalm-api
 * @psalm-suppress MissingTemplateParam
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class ParameterBag extends ArrayIterator
{
    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var array
     */
    private array $parameters;

    /**
     * @psalm-api
     *
     * @param array $parameters
     * @return static
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
    }

    /**
     * @psalm-api
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @psalm-api
     *
     * @param array $parameters
     * @return void
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @psalm-api
     *
     * @param string $key
     * @return bool
     */
    public function hasParameter(string $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * @psalm-api
     *
     * @param string $key
     * @return mixed
     */
    public function getParameter(string $key)
    {
        return $this->offsetGet($key);
    }

    /**
     * @psalm-api
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setParameter(string $key, $value): void
    {
        $this->offsetSet($key, $value);
    }
}
