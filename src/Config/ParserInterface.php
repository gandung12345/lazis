<?php

declare(strict_types=1);

namespace Schnell\Config;

use Schnell\Config\Ast\AstInterface;
use Schnell\Config\Node\NodeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface ParserInterface
{
    /**
     * @return array
     */
    public function getTokens(): array;

    /**
     * @param int $index
     * @return \Schnell\Config\Node\NodeInterface|null
     */
    public function getTokensAt(int $index): NodeInterface|null;

    /**
     * @param array $tokens
     * @return void
     */
    public function setTokens(array $tokens): void;

    /**
     * @return \Schnell\Config\Node\NodeInterface|null
     */
    public function getToken(): NodeInterface|null;

    /**
     * @param \Schnell\Config\Node\NodeInterface|null $token
     * @return void
     */
    public function setToken(NodeInterface|null $token): void;

    /**
     * @return void
     */
    public function resetToken(): void;

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

    /**
     * @return \Schnell\Config\Ast\AstInterface
     */
    public function getRoot(): AstInterface;

    /**
     * @param \Schnell\Config\Ast\AstInterface $root
     * @return void
     */
    public function setRoot(AstInterface $root): void;

    /**
     * @return void
     */
    public function parse(): void;

    /**
     * @return \Schnell\Config\Ast\AstInterface
     */
    public function ast(): AstInterface;
}
