<?php

declare(strict_types=1);

namespace Schnell\Config\Ast\Visitor;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Block extends AbstractVisitor
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return '(block-visitor)';
    }

    /**
     * {@inheritdoc}
     */
    public function resolve()
    {
        return $this->ast->getValue()->getValue();
    }
}
