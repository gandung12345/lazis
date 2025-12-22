<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Node\Expr;

use Schnell\Http\FQL\Node\ExprNodeInterface;
use Schnell\Http\FQL\Node\NodeTypes;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class OrX implements ExprNodeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType(): int
    {
        return NodeTypes::ORX;
    }
}
