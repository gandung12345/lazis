<?php

declare(strict_types=1);

namespace Schnell\Tests\Config\Ast\Node;

use Schnell\Config\Ast\Node\AbstractNode;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ConcreteAstNode extends AbstractNode
{
    /**
     * @param int $type
     * @return static
     */
    public function __construct(int $type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return '(concrete)';
    }
}
