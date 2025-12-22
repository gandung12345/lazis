<?php

declare(strict_types=1);

namespace Schnell\Hydrator;

use ReflectionClass;
use Doctrine\ORM\Proxy\InternalProxy;

use function get_parent_class;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
trait HydratorTrait
{
    /**
     * @param \Doctrine\ORM\Proxy\InternalProxy $proxyObject
     * @return array
     */
    private function getProxyClassProperties(InternalProxy $proxyObject): array
    {
        $reflection = new ReflectionClass(get_parent_class($proxyObject));
        return $reflection->getProperties();
    }
}
