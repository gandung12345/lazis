<?php

declare(strict_types=1);

namespace Schnell\Http\FQL;

use Schnell\Http\FQL\Node\NodeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface LexerInterface
{
    /**
     * @return void
     */
    public function lex(): void;

    /**
     * @return string|null
     */
    public function getBuffer(): ?string;

    /**
     * @param string|null $buffer
     * @return void
     */
    public function setBuffer(?string $buffer): void;

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
     * @param \Schnell\Http\FQL\Node\NodeInterface $node
     * @return void
     */
    public function addToken(NodeInterface $node): void;

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
    public function incrementPosition(): void;
}
