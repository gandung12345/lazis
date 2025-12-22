<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Ast\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class NodeTypes
{
    /**
     * @internal
     * @psalm-api
     *
     * @return static
     */
    private function __construct()
    {
    }

    /**
     * @var int
     */
    public const int ROOT = 1;

    /**
     * @var int
     */
    public const int EXPR_ANDX = 2;

    /**
     * @var int
     */
    public const int EXPR_EQUALX = 3;

    /**
     * @var int
     */
    public const int EXPR_GREATER_OR_EQUALX = 4;

    /**
     * @var int
     */
    public const int EXPR_GREATERX = 5;

    /**
     * @var int
     */
    public const int EXPR_LESS_OR_EQUALX = 6;

    /**
     * @var int
     */
    public const int EXPR_LESSX = 7;

    /**
     * @var int
     */
    public const int EXPR_NOT_EQUALX = 8;

    /**
     * @var int
     */
    public const int EXPR_ORX = 9;

    /**
     * @var int
     */
    public const int KEY_VALUE = 10;

    /**
     * @var int
     */
    public const int EXPR_LIKEX = 11;
}
