<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Ast\Node;

use Schnell\Http\FQL\Ast\AstInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface NodeInterface
{
    /**
     * @psalm-api
     *
     * @return int
     */
    public function getType(): int;

    /**
     * @psalm-api
     *
     * @param \Schnell\Http\FQL\Ast\AstInterface $ast
     * @return array|null
     */
    public function getInvokable(AstInterface $ast): ?array;
}
