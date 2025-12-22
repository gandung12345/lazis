<?php

declare(strict_types=1);

namespace Schnell\Decorator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface StringifiedDecoratorInterface extends DecoratorInterface
{
    /**
     * @return string
     */
    public function __toString(): string;
}
