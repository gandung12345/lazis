<?php

declare(strict_types=1);

namespace Schnell\Config;

use Schnell\Config\Node\NodeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface LexerInterface
{
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
     * @return void
     */
    public function decrementPosition(): void;

    /**
     * @return void
     */
    public function incrementPosition(): void;

    /**
     * @return int
     */
    public function getCols(): int;

    /**
     * @param int $cols
     * @return void
     */
    public function setCols(int $cols): void;

    /**
     * @return void
     */
    public function resetCols(): void;

    /**
     * @return string|null
     */
    public function getToken(): string|null;

    /**
     * @param string|null $token
     * @return void
     */
    public function setToken(string|null $token): void;

    /**
     * @return string
     */
    public function getBuffer(): string;

    /**
     * @param string $buffer
     * @return void
     */
    public function setBuffer(string $buffer): void;

    /**
     * @return bool
     */
    public function getScoped(): bool;

    /**
     * @param bool $scoped
     * @return void
     */
    public function setScoped(bool $scoped): void;

    /**
     * @return bool
     */
    public function getArrayScoped(): bool;

    /**
     * @param bool $arrayScoped
     * @return void
     */
    public function setArrayScoped(bool $arrayScoped): void;

    /**
     * @return int
     */
    public function getScopeCount(): int;

    /**
     * @param int $scopeCount
     * @return void
     */
    public function setScopeCount(int $scopeCount): void;

    /**
     * @param int $arrayScopeCount
     * @return void
     */
    public function setArrayScopeCount(int $arrayScopeCount): void;

    /**
     * @return array
     */
    public function getTokens(): array;

    /**
     * @param array $tokens
     * @return void
     */
    public function setTokens(array $tokens): void;

    /**
     * @param \Schnell\Config\Node\NodeInterface $node
     * @return void
     */
    public function addNode(NodeInterface $node): void;

    /**
     * @return int
     */
    public function getNewlines(): int;

    /**
     * @param int $newlines
     * @return void
     */
    public function setNewlines(int $newlines): void;

    /**
     * @return void
     */
    public function incrementNewlines(): void;

    /**
     * @return void
     */
    public function incrementCols(): void;

    /**
     * @return void
     */
    public function incrementScopeCount(): void;

    /**
     * @return void
     */
    public function decrementScopeCount(): void;

    /**
     * @return void
     */
    public function incrementArrayScopeCount(): void;

    /**
     * @return string|null
     */
    public function getStrstart(): string|null;

    /**
     * @param string|null $strstart
     * @return void
     */
    public function setStrstart(string|null $strstart): void;

    /**
     * @psalm-api
     *
     * @return array
     */
    public function getReservedKeywords(): array;

    /**
     * @param array $keywords
     * @return void
     */
    public function setReservedKeywords(array $keywords): void;

    /**
     * @param int $index
     * @return \Schnell\Config\Node\NodeInterface|null
     */
    public function getNodeAt(int $index): NodeInterface|null;

    /**
     * @return \Schnell\Config\Node\NodeInterface|null
     */
    public function getLastNode(): NodeInterface|null;

    /**
     * @return void
     */
    public function lex(): void;
}
