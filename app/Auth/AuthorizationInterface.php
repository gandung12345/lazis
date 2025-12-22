<?php

declare(strict_types=1);

namespace Lazis\Api\Auth;

use Psr\Http\Message\ServerRequestInterface;
use Schnell\ContainerInterface;
use Schnell\Config\ConfigInterface;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface AuthorizationInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Lazis\Api\Auth\TokenObject
     */
    public function authorize(ServerRequestInterface $request): TokenObject;

    /**
     * @return \Schnell\Schema\SchemaInterface|null
     */
    public function getTokenSchema(): ?SchemaInterface;

    /**
     * @param \Schnell\Schema\SchemaInterface|null $schema
     * @return void
     */
    public function setTokenSchema(?SchemaInterface $schema): void;

    /**
     * @return \Schnell\ContainerInterface|null
     */
    public function getContainer(): ?ContainerInterface;

    /**
     * @param \Schnell\ContainerInterface|null $container
     * @return void
     */
    public function setContainer(?ContainerInterface $container): void;

    /**
     * @return \Schnell\Config\ConfigInterface|null
     */
    public function getConfig(): ?ConfigInterface;

    /**
     * @param \Schnell\Config\ConfigInterface|null $config
     * @return void
     */
    public function setConfig(?ConfigInterface $config): void;
}
