<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Node\Symbol;

use Schnell\Http\FQL\Node\NodeTypes;
use Schnell\Http\FQL\Node\SymbolNodeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Colon implements SymbolNodeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType(): int
    {
        return NodeTypes::COLON;
    }
}
