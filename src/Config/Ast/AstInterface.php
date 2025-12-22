<?php

declare(strict_types=1);

namespace Schnell\Config\Ast;

use Schnell\Config\Ast\Visitor\VisitorInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface AstInterface
{
    /**
     * @psalm-api
     *
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     * @return void
     */
    public function setValue($value): void;

    /**
     * @return \Schnell\Config\Ast\Visitor\VisitorInterface|null
     */
    public function getVisitor(): VisitorInterface|null;

    /**
     * @param \Schnell\Config\Ast\Visitor\VisitorInterface|null $visitor
     * @return void
     */
    public function setVisitor(VisitorInterface|null $visitor): void;

    /**
     * @psalm-api
     *
     * @return array
     */
    public function getChilds(): array;

    /**
     * @param array $childs
     * @return void
     */
    public function setChilds(array $childs): void;

    /**
     * @psalm-api
     *
     * @param \Schnell\Config\Ast\AstInterface $child
     * @return void
     */
    public function addChild(AstInterface $child): void;

    /**
     * @param int $index
     * @return \Schnell\Config\Ast\AstInterface|null
     */
    public function getChildAt(int $index): AstInterface|null;

    /**
     * @psalm-api
     *
     * @return \Schnell\Config\Ast\AstInterface|null
     */
    public function getLastChild(): AstInterface|null;

    /**
     * @psalm-api
     *
     * @return mixed
     */
    public function visit();
}
