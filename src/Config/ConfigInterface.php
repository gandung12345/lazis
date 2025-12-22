<?php

declare(strict_types=1);

namespace Schnell\Config;

use Schnell\Config\Ast\AstInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface ConfigInterface
{
    /**
     * @return \Schnell\Config\Ast\AstInterface
     */
    public function getAst(): AstInterface;

    /**
     * @param \Schnell\Config\Ast\AstInterface $ast
     * @return void
     */
    public function setAst(AstInterface $ast): void;

    /**
     * @return array
     */
    public function getMap(): array;

    /**
     * @param array $map
     * @return void
     */
    public function setMap(array $map): void;

    /**
     * @return string|null
     */
    public function getKey(): string|null;

    /**
     * @param string|null $key
     * @return void
     */
    public function setKey(string|null $key): void;

    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name);
}
