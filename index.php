<?php

declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use Lazis\Api\Middleware\RoleMiddleware;
use Schnell\Container;
use Schnell\Kernel;
use Schnell\Bridge\Cache\CacheBridge;
use Schnell\Bridge\Doctrine\DoctrineBridge;
use Schnell\Bridge\Mapper\MapperBridge;
use Schnell\Bridge\SlimCacheProvider\SlimCacheProviderBridge;
use Schnell\Bridge\Swagger\SwaggerBridge;
use Schnell\Config\ConfigFactory;
use Schnell\Controller\ControllerPool;
use Schnell\Controller\ControllerResolver;
use Schnell\Middleware\CorsMiddleware;
use Schnell\Middleware\HttpErrorMiddleware;
use Schnell\Middleware\ContentTypeMiddleware;

use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

$configFactory = new ConfigFactory();
$configFactory->importBulk([
    './config/app.conf',
    './config/controller.conf',
    './config/database.conf',
    './config/notifier.conf',
    './config/route.conf',
    './config/bridge/cache.conf',
    './config/bridge/doctrine.conf',
    './config/bridge/swagger.conf'
]);

$config = $configFactory->getConfig();
$container = new Container();
$request = ServerRequestCreatorFactory::create()
    ->createServerRequestFromGlobals();
$controllerPool = new ControllerPool(
    $container,
    $config,
    new SplObjectStorage()
);

$controllerPool->collect();

AppFactory::setContainer($container);

$app = AppFactory::create();

$controllerResolver = new ControllerResolver($controllerPool, $app);
$controllerResolver->add(new ContentTypeMiddleware($controllerPool));
$controllerResolver->addHttpCache();
//$controllerResolver->add(new RoleMiddleware($controllerPool));
$controllerResolver->addRoutingMiddleware();
$controllerResolver->add(new CorsMiddleware($controllerPool));
$controllerResolver->addBodyParsingMiddleware();
$controllerResolver->add(new HttpErrorMiddleware($controllerPool));
$controllerResolver->resolve($request);

registerShutdownHandler($request, shutdownHandlerCallback($request));

$kernel = new Kernel($config, $container, $controllerResolver);
$kernel->addExtension(new CacheBridge(), getcwd());
$kernel->addExtension(new DoctrineBridge(), getcwd());
$kernel->addExtension(new MapperBridge(), getcwd());
$kernel->addExtension(new SlimCacheProviderBridge(), getcwd());
$kernel->addExtension(new SwaggerBridge(), getcwd());
$kernel->load();
$kernel->handle($request);
