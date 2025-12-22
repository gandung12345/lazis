<?php

declare(strict_types=1);

namespace Schnell\Bridge\Doctrine;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Schnell\ContainerInterface;
use Schnell\Bridge\AbstractBridge;
use Schnell\Bridge\Doctrine\Type\DateTimeType;
use Schnell\Config\ConfigInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

use function array_map;
use function class_exists;
use function sprintf;

// help opcache.preload discover always-needed symbols
// @codeCoverageIgnoreStart
// phpcs:disable
class_exists(DriverManager::class);
class_exists(EntityManager::class);
class_exists(ORMSetup::class);
class_exists(AbstractBridge::class);
class_exists(ArrayAdapter::class);
// phpcs:enable
// @codeCoverageIgnoreEnd

/**
 * @psalm-api
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DoctrineBridge extends AbstractBridge
{
    /**
     * @return void
     */
    public function overrideType(): void
    {
        Type::overrideType('date', DateTimeType::class);
    }

    /**
     * {@inheritDoc}
     */
    public function load(): void
    {
        $this->overrideType();

        /**
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress PossiblyNullArgument
         */
        $this->getContainer()->registerCallback(
            EntityManagerInterface::class,
            function (
                ContainerInterface $container,
                ConfigInterface $config
            ): EntityManagerInterface {
                $cacheDir = sprintf(
                    "%s%s%s",
                    $this->getBasePath(),
                    DIRECTORY_SEPARATOR,
                    $config->get('doctrine.cache_dir')
                );

                $proxyDir = sprintf(
                    "%s%s%s",
                    $this->getBasePath(),
                    DIRECTORY_SEPARATOR,
                    $config->get('doctrine.proxy_dir')
                );

                $queryCache = $config->get('doctrine.dev_mode')
                    ? new ArrayAdapter()
                    : new PhpFilesAdapter(
                        'doctrine_queries',
                        0,
                        $cacheDir
                    );

                $metadataCache = $config->get('doctrine.dev_mode')
                    ? new ArrayAdapter()
                    : new PhpFilesAdapter(
                        'doctrine_metadata',
                        0,
                        $cacheDir
                    );

                $metadataFn = function (string $path): string {
                    return sprintf(
                        "%s%s%s",
                        $this->getBasePath(),
                        DIRECTORY_SEPARATOR,
                        $path
                    );
                };

                $ormConf = new Configuration();
                $ormConf->setMetadataCache($metadataCache);

                $driverImpl = new AttributeDriver(
                    array_map($metadataFn, $config->get('doctrine.metadata_dirs')),
                    true
                );

                $ormConf->setMetadataCache($metadataCache);
                $ormConf->setMetadataDriverImpl($driverImpl);
                $ormConf->setQueryCache($queryCache);
                $ormConf->setProxyDir($proxyDir);
                $ormConf->setProxyNamespace($config->get('doctrine.proxy_namespace'));
                $ormConf->setAutoGenerateProxyClasses($config->get('doctrine.dev_mode'));

                $options = [
                    'driver' => $config->get('database.driver'),
                    'host' => $config->get('database.host'),
                    'port' => $config->get('database.port'),
                    'dbname' => $config->get('database.schema'),
                    'user' => $config->get('database.user'),
                    'password' => $config->get('database.password'),
                    'charset' => $config->get('database.charset')
                ];

                $entityManager = new EntityManager(
                    DriverManager::getConnection($options),
                    $ormConf
                );

                $entityManager
                    ->getConnection()
                    ->getDatabasePlatform()
                    ->registerDoctrineTypeMapping('date', 'date');

                return $entityManager;
            },
            [$this->getContainer(), $this->getConfig()]
        );

        /** @psalm-suppress PossiblyNullReference */
        $this->getContainer()->alias(
            EntityManagerInterface::class,
            $this->getAlias()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias(): string
    {
        return 'entity-manager';
    }
}
