<?php

declare(strict_types=1);

namespace Schnell\Bridge\Swagger;

use OpenApi\Annotations\OpenApi;
use OpenApi\Generator as OpenApiGenerator;
use Psr\Log\NullLogger;
use Schnell\ContainerInterface;
use Schnell\Bridge\AbstractBridge;
use Schnell\Config\ConfigInterface;

/**
 * @psalm-api
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class SwaggerBridge extends AbstractBridge
{
    /**
     * {@inheritdoc}
     */
    public function load(): void
    {
        /** @psalm-suppress PossiblyNullReference */
        $this->getContainer()->registerCallback(
            OpenApi::class,
            function (
                ContainerInterface $container,
                ConfigInterface $config
            ): ?OpenApi {
                /** @psalm-suppress PossiblyNullArgument */
                $normalizedDirs = array_map(
                    fn(string $dir) => sprintf(
                        '%s%s%s',
                        $this->getBasePath(),
                        DIRECTORY_SEPARATOR,
                        $dir
                    ),
                    $config->get('swagger.dirs')
                );

                /** @psalm-ignore-nullable-return */
                return OpenApiGenerator::scan($normalizedDirs, [
                    'logger' => new NullLogger()
                ]);
            },
            [$this->getContainer(), $this->getConfig()]
        );

        /** @psalm-suppress PossiblyNullReference */
        $this->getContainer()->alias(OpenApi::class, $this->getAlias());
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'swagger';
    }
}
