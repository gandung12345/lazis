<?php

declare(strict_types=1);

namespace Schnell\Hydrator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface HydratorInterface
{
    /**
     * @psalm-api
     *
     * @param mixed $value
     * @return mixed
     * @throws \Schnell\Exception\HydratorException
     */
    public function hydrate($value);
}
