<?php

declare(strict_types=1);

namespace Lazis\Api\Auth;

use Lazis\Api\Auth\Exception\AuthorizationException;
use Psr\Http\Message\ServerRequestInterface;
use Schnell\ContainerInterface;
use Schnell\Config\ConfigInterface;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Authorization implements AuthorizationInterface
{
    /**
     * @var \Schnell\ContainerInterface|null
     */
    private ?ContainerInterface $container;

    /**
     * @var \Schnell\Config\ConfigInterface|null
     */
    private ?ConfigInterface $config;

    /**
     * @var \Schnell\Schema\SchemaInterface|null
     */
    private ?SchemaInterface $schema;

    /**
     * @param \Schnell\ContainerInterface $container
     * @param \Schnell\Config\ConfigInterface $config
     * @param \Schnell\Schema\SchemaInterface|null $schema
     * @return static
     */
    public function __construct(
        ?ContainerInterface $container = null,
        ?ConfigInterface $config = null,
        ?SchemaInterface $schema = null
    ) {
        $this->setContainer($container);
        $this->setConfig($config);
        $this->setTokenSchema($schema);
    }

    /**
     * {@inheritDoc}
     */
    public function authorize(ServerRequestInterface $request): TokenObject
    {
        $tokenObject = $this->getTokenObjectFromCache();

        if (null === $tokenObject) {
            $tokenObject = $this->generateTokenObject();

            if (null === $tokenObject) {
                throw new AuthorizationException(
                    $request,
                    sprintf(
                        'User with email \'%s\' and password \'%s\' not exists',
                        $this->getTokenSchema()->getEmail(),
                        $this->getTokenSchema()->getPassword()
                    )
                );
            }

            $this->storeTokenObjectIntoCache($tokenObject);
        }

        return $tokenObject;
    }

    /**
     * {@inheritDoc}
     */
    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(?ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig(): ?ConfigInterface
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
    public function setConfig(?ConfigInterface $config): void
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenSchema(): ?SchemaInterface
    {
        return $this->schema;
    }

    /**
     * {@inheritDoc}
     */
    public function setTokenSchema(?SchemaInterface $schema): void
    {
        $this->schema = $schema;
    }

    /**
     * @return \Lazis\Api\Auth\TokenObject|null
     */
    private function getTokenObjectFromCache(): ?TokenObject
    {
        $cacheTag = base64_encode(sprintf(
            '%s:%s',
            $this->getTokenSchema()->getEmail(),
            $this->getTokenSchema()->getPassword()
        ));

        $cacheItem = $this->getContainer()
            ->get('cache')
            ->getItem($cacheTag);

        return false === $cacheItem->isHit() ? null : $cacheItem->get();
    }

    /**
     * @return \Lazis\Api\Auth\TokenObject|null
     */
    private function generateTokenObject(): ?TokenObject
    {
        $tokenGenerator = new TokenGenerator(
            $this->getContainer(),
            $this->getConfig()
        );

        $tokenObject = $tokenGenerator->generate($this->getTokenSchema());

        if (null === $tokenObject) {
            return null;
        }

        return $tokenObject;
    }

    /**
     * @param \Lazis\Api\Auth\TokenObject $tokenObject
     * @return bool
     */
    private function storeTokenObjectIntoCache(?TokenObject $tokenObject): bool
    {
        $cacheTag = base64_encode(sprintf(
            '%s:%s',
            $this->getTokenSchema()->getEmail(),
            $this->getTokenSchema()->getPassword()
        ));

        $cacheItem = $this->getContainer()
            ->get('cache')
            ->getItem($cacheTag);

        $cacheItem->set($tokenObject);
        $cacheItem->expiresAfter($tokenObject->getLifetime());

        return $this->getContainer()
            ->get('cache')
            ->save($cacheItem);
    }
}
