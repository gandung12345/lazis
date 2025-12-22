<?php

declare(strict_types=1);

namespace Schnell\Http\FQL;

use Doctrine\ORM\QueryBuilder;
use Schnell\Entity\EntityInterface;
use Schnell\Http\FQL\Ast\AstInterface;
use Schnell\Http\FQL\Node\NodeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface ParserInterface
{
    /**
     * @return array|null
     */
    public function getTokens(): ?array;

    /**
     * @param array|null $tokens
     * @return void
     */
    public function setTokens(?array $tokens): void;

    /**
     * @psalm-api
     * @psalm-suppress PossiblyUnusedMethod
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
     * @return \Schnell\Entity\EntityInterface
     */
    public function getEntity(): ?EntityInterface;

    /**
     * @param \Schnell\Entity\EntityInterface|null
     */
    public function setEntity(?EntityInterface $entity): void;

    /**
     * @return int
     */
    public function getPosition(): int;

    /**
     * @param int $position
     * @return void
     */
    public function setPosition(int $position): void;

    /**
     * @return bool|null
     */
    public function getScoped(): ?bool;

    /**
     * @param bool|null $scoped
     * @return void
     */
    public function setScoped(?bool $scoped): void;

    /**
     * @return int|null
     */
    public function getScopeCount(): ?int;

    /**
     * @param int|null $scopeCount
     * @return void
     */
    public function setScopeCount(?int $scopeCount): void;

    /**
     * @return void
     */
    public function decrementScopeCount(): void;

    /**
     * @return void
     */
    public function incrementScopeCount(): void;

    /**
     * @return \Schnell\Http\FQL\Ast\AstInterface|null
     */
    public function getAst(): ?AstInterface;

    /**
     * @param \Schnell\Http\FQL\Ast\AstInterface|null $ast
     * @return void
     */
    public function setAst(?AstInterface $ast): void;

    /**
     * @return \Schnell\Http\FQL\ParameterBag|null
     */
    public function getParameterBag(): ?ParameterBag;

    /**
     * @param \Schnell\Http\FQL\ParameterBag|null $parameterBag
     * @return void
     */
    public function setParameterBag(?ParameterBag $parameterBag): void;

    /**
     * @return void
     */
    public function parse(): void;
}
