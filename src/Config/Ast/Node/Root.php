<?php

declare(strict_types=1);

namespace Schnell\Config\Ast\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Root extends AbstractNode
{
    /**
     * @psalm-api
     *
     * @return static
     */
    public function __construct()
    {
        $this->type = NodeTypes::ROOT;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return '(root)';
    }
}
