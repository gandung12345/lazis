<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Ast;

use Schnell\Http\FQL\ParameterBag;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface ParameterBagAwareInterface
{
    /**
     * @return \Schnell\Http\FQL\ParameterBag|null
     */
    public function getParameterBag(): ?ParameterBag;

    /**
     * @param \Schnell\Http\FQL\ParameterBag|null $parameterBag
     * @return void
     */
    public function setParameterBag(?ParameterBag $parameterBag): void;
}
