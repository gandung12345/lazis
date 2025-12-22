<?php

declare(strict_types=1);

namespace Lazis\Api\Auth;

use Lazis\Api\Schema\TokenSchema;
use Schnell\ContainerInterface;
use Schnell\Config\ConfigInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface TokenGeneratorInterface
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
     * @return \Schnell\Config\ConfigInterface
     */
    public function getConfig(): ConfigInterface;

    /**
     * @param \Schnell\Config\ConfigInterface $config
     * @return void
     */
    public function setConfig(ConfigInterface $config): void;

    /**
     * @return \Lazis\Api\Auth\TokenObject|null
     */
    public function generate(TokenSchema $schema): ?TokenObject;
}
