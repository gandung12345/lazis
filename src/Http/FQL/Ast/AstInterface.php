<?php

declare(strict_types=1);

namespace Schnell\Http\FQL\Ast;

use Doctrine\ORM\QueryBuilder;
use Schnell\Http\FQL\ParameterBag;
use Schnell\Http\FQL\Ast\Node\NodeInterface as AstNodeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface AstInterface extends ParameterBagAwareInterface
{
    /**
     * @return \Schnell\Http\FQL\Ast\Node\NodeInterface|null
     */
    public function getValue(): ?AstNodeInterface;

    /**
     * @param \Schnell\Http\FQL\Ast\Node\NodeInterface|null $value
     * @return void
     */
    public function setValue(?AstNodeInterface $value): void;

    /**
     * @return \Schnell\Http\FQL\Ast\AstInterface|null
     */
    public function getParent(): ?AstInterface;

    /**
     * @param \Schnell\Http\FQL\Ast\AstInterface|null $parent
     * @return void
     */
    public function setParent(?AstInterface $parent): void;

    /**
     * @psalm-api
     *
     * @return \Doctrine\ORM\QueryBuilder|null
     */
    public function getQueryBuilder(): ?QueryBuilder;

    /**
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder
     * @return void
     */
    public function setQueryBuilder(?QueryBuilder $queryBuilder): void;

    /**
     * @return array|null
     */
    public function getChilds(): ?array;

    /**
     * @param array|null $childs
     * @return void
     */
    public function setChilds(?array $childs): void;

    /**
     * @psalm-api
     *
     * @param \Schnell\Http\FQL\Ast\AstInterface|null $child
     * @return void
     */
    public function addChild(?AstInterface $child): void;

    /**
     * @psalm-api
     *
     * @param string|null $queryAlias
     * @return mixed
     */
    public function visit(?string $queryAlias);
}
