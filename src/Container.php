<?php

declare(strict_types=1);

namespace Schnell;

use DI\Container as CoreContainer;
use Schnell\Exception\NotFoundException;

use function array_key_exists;
use function class_exists;
use function call_user_func_array;

// help opcache.preload discover always-needed symbols
// @codeCoverageIgnoreStart
// phpcs:disable
class_exists(CoreContainer::class);
class_exists(NotFoundException::class);
// phpcs:enable
// @codeCoverageIgnoreEnd

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Container implements ContainerInterface
{
    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var array
     */
    private array $instances = [];

    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var array
     */
    private array $aliasMap = [];

    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var \DI\Container
     */
    private CoreContainer $container;

    /**
     * @psalm-api
     *
     * @param \DI\Container|null $container
     * @return static
     */
    public function __construct(CoreContainer|null $container = null)
    {
        $this->container = $container ?? new CoreContainer();
    }

    /**
     * {@inheritDoc}
     */
    public function registerCallback(
        string $class,
        callable $fn,
        array $fnParam
    ): void {
        $this->instances[$class] = call_user_func_array($fn, $fnParam);
    }

    /**
     * @psalm-api
     *
     * @param string $class
     * @return void
     */
    public function autowire(string $class)
    {
        $this->instances[$class] = $this->container->get($class);
    }

    /**
     * {@inheritDoc}
     */
    public function direct(string $class, object $instance): void
    {
        $this->instances[$class] = $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function alias(string $class, string $alias): void
    {
        $this->aliasMap[$alias] = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $id): bool
    {
        $class = array_key_exists($id, $this->aliasMap)
            ? $this->aliasMap[$id]
            : $id;

        return array_key_exists($class, $this->instances);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException(
                sprintf("Object with identifier '%s' not found.", $id)
            );
        }

        $className = isset($this->aliasMap[$id])
            ? $this->aliasMap[$id]
            : $id;

        return $this->instances[$className];
    }
}
