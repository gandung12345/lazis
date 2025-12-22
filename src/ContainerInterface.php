<?php

declare(strict_types=1);

namespace Schnell;

use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface ContainerInterface extends PsrContainerInterface
{
    /**
     * @psalm-api
     *
     * @param string $class
     * @param callable $fn
     * @param array $fnParam
     * @return void
     */
    public function registerCallback(string $class, callable $fn, array $fnParam): void;

    /**
     * @psalm-api
     *
     * @param string $class
     * @param string $alias
     * @return void
     */
    public function alias(string $class, string $alias): void;

    /**
     * @psalm-api
     *
     * @param string $class
     * @param object $instance
     * @return void
     */
    public function direct(string $class, object $instance): void;
}
