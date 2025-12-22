<?php

declare(strict_types=1);

namespace Schnell\Config\Ast\Visitor;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface VisitorInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @psalm-api
     *
     * @return mixed
     */
    public function resolve();

    /**
     * @return string
     */
    public function __toString(): string;
}
