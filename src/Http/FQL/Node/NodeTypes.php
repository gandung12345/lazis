<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Node;

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
    public const int ANDX = 1;

    /**
     * @var int
     */
    public const int EQUALX = 2;

    /**
     * @var int
     */
    public const int GREATER_OR_EQUALX = 3;

    /**
     * @var int
     */
    public const int GREATERX = 4;

    /**
     * @var int
     */
    public const int LESS_OR_EQUALX = 5;

    /**
     * @var int
     */
    public const int LESSX = 6;

    /**
     * @var int
     */
    public const int NOT_EQUALX = 7;

    /**
     * @var int
     */
    public const int ORX = 8;

    /**
     * @var int
     */
    public const int INTEGER = 9;

    /**
     * @var int
     */
    public const int STR = 10;

    /**
     * @var int
     */
    public const int CLOSE_SQUARE_BRACE = 11;

    /**
     * @var int
     */
    public const int COLON = 12;

    /**
     * @var int
     */
    public const int COMMA = 13;

    /**
     * @var int
     */
    public const int OPEN_SQUARE_BRACE = 14;

    /**
     * @var int
     */
    public const int LIKEX = 15;
}
