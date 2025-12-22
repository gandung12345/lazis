<?php

declare(strict_types=1);

namespace Schnell\Bridge\Mapper;

use Doctrine\ORM\EntityManagerInterface;
use Schnell\ContainerInterface;
use Schnell\Bridge\AbstractBridge;
use Schnell\Config\ConfigInterface;
use Schnell\Exception\ExtensionException;
use Schnell\Mapper\Mapper;
use Schnell\Mapper\MapperInterface;

use function class_exists;

// help opcache.preload discover always-needed symbols
// @codeCoverageIgnoreStart
// phpcs:disable
class_exists(AbstractBridge::class);
class_exists(ExtensionException::class);
class_exists(Mapper::class);
// phpcs:enable
// @codeCoverageIgnoreEnd

/**
 * @psalm-api
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class MapperBridge extends AbstractBridge
{
    /**
     * {@inheritdoc}
     */
    public function load(): void
    {
        /** @psalm-suppress PossiblyNullReference */
        if (false === $this->getContainer()->has(EntityManagerInterface::class)) {
            throw new ExtensionException(
                sprintf(
                    "Object instance with type '%s' not found.",
                    EntityManagerInterface::class
                )
            );
        }

        /** @psalm-suppress PossiblyNullReference */
        $this->getContainer()->registerCallback(
            MapperInterface::class,
            function (?ContainerInterface $container = null): MapperInterface {
                return new Mapper(
                    $container->get(EntityManagerInterface::class)
                );
            },
            [$this->getContainer()]
        );

        /** @psalm-suppress PossiblyNullReference */
        $this->getContainer()->alias(MapperInterface::class, $this->getAlias());
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'mapper';
    }
}
