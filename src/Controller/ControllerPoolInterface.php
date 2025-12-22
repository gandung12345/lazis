<?php

declare(strict_types=1);

namespace Schnell\Controller;

use SplObjectStorage;
use Schnell\ContainerInterface;
use Schnell\Config\ConfigInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function class_exists;

// help opcache.preload discover always-needed symbols
// phpcs:disable
class_exists(SplObjectStorage::class);
class_exists(Container::class);
// phpcs:enable

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface ControllerPoolInterface
{
    /**
     * @return \Schnell\ContainerInterface
     */
    public function getContainer(): ContainerInterface;

    /**
     * @param \Schnell\ContainerInterface $container
     * @return void
     */
    public function setContainer(ContainerInterface $container): void;

    /**
     * @psalm-api
     *
     * @return \SplObjectStorage
     */
    public function getPool(): SplObjectStorage;

    /**
     * @psalm-api
     *
     * @param object $key
     * @return mixed
     */
    public function getPoolAt(object $key);

    /**
     * @param \SplObjectStorage $pool
     * @return void
     */
    public function setPool(SplObjectStorage $pool): void;

    /**
     * @param object $key
     * @param mixed $value
     * @return void
     */
    public function addPoolAt(object $key, $value): void;

    /**
     * @return \Schnell\Config\ConfigInterface
     */
    public function getConfig(): ConfigInterface;

    /**
     * @param \Schnell\Config\ConfigInterface $config
     * @return void
     */
    public function setConfig(ConfigInterface $config): void;

    /**
     * @psalm-api
     *
     * @return void
     */
    public function collect(): void;
}
