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
class ExprLessOrEqualX implements ExprNodeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOperator(): string
    {
        return '<=';
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): int
    {
        return NodeTypes::EXPR_LESS_OR_EQUALX;
    }

    /**
     * {@inheritdoc}
     */
    public function getInvokable(AstInterface $ast): ?array
    {
        /** @psalm-suppress PossiblyNullReference */
        return [$ast->getQueryBuilder()->expr(), 'lte'];
    }
}
