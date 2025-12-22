<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Ast\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface ExprNodeInterface extends NodeInterface
{
    /**
     * @psalm-api
     *
     * @return string
     */
    public function getOperator(): string;
}
