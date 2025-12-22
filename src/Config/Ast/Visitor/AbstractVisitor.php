<?php

declare(strict_types=1);

namespace Schnell\Config\Ast\Visitor;

use Schnell\Config\Ast\AstInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
abstract class AbstractVisitor implements VisitorInterface
{
    /**
     * @var \Schnell\Config\Ast\AstInterface
     */
    protected $ast;

    /**
     * @psalm-api
     *
     * @param \Schnell\Config\Ast\AstInterface $ast
     * @return static
     */
    public function __construct(AstInterface $ast)
    {
        $this->ast = $ast;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getName(): string;

    /**
     * {@inheritdoc}
     */
    abstract public function resolve();
}
