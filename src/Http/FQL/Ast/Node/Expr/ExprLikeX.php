<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Ast\Node\Expr;

use Schnell\Http\FQL\Ast\AstInterface;
use Schnell\Http\FQL\Ast\Node\ExprNodeInterface;
use Schnell\Http\FQL\Ast\Node\NodeTypes;

/**
 * @psalm-api
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ExprLikeX implements ExprNodeInterface
{
    /**
     * {@inheritDoc}
     */
    public function getOperator(): string
    {
        return 'LIKE';
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): int
    {
        return NodeTypes::EXPR_LIKEX;
    }

    /**
     * {@inheritDoc}
     */
    public function getInvokable(AstInterface $ast): ?array
    {
        return [$ast->getQueryBuilder()->expr(), 'like'];
    }
}
