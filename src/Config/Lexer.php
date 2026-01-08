<?php

declare(strict_types=1);

namespace Schnell\Config;

use Schnell\Exception\ConfigLexerException;
use Schnell\Config\Node\Assign;
use Schnell\Config\Node\Arr as ArrayNode;
use Schnell\Config\Node\Block;
use Schnell\Config\Node\Boolean;
use Schnell\Config\Node\Identifier;
use Schnell\Config\Node\Integer;
use Schnell\Config\Node\Str;
use Schnell\Config\Node\NodeInterface;
use Schnell\Config\Node\NodeTypes;

use function class_exists;
use function intval;
use function is_numeric;
use function sprintf;

// help opcache.preload discover always-needed symbols
// @codeCoverageIgnoreStart
// phpcs:disable
class_exists(ConfigLexerException::class);
class_exists(Assign::class);
class_exists(ArrayNode::class);
class_exists(Block::class);
class_exists(Boolean::class);
class_exists(Identifier::class);
class_exists(Integer::class);
class_exists(Str::class);
// phpcs:enable
// @codeCoverageIgnoreEnd

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Lexer implements LexerInterface
{
    /**
     * @var int
     */
    private int $position;

    /**
     * @var int
     */
    private int $cols;

    /**
     * @var string|null
     */
    private ?string $token;

    /**
     * @var string
     */
    private string $buffer;

    /**
     * @var bool
     */
    private bool $scoped;

    /**
     * @var bool
     */
    private bool $arrayScoped;

    /**
     * @var int
     */
    private int $scopeCount;

    /**
     * @var int
     */
    private int $arrayScopeCount;

    /**
     * @var array<\Schnell\Config\Node\NodeInterface>
     */
    private array $tokens;

    /**
     * @var int
     */
    private int $newlines;

    /**
     * @var string|null
     */
    private ?string $strstart;

    /**
     * @var array<int, string>
     */
    private array $reservedKeywords;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $buffer)
    {
        $this->initialize($buffer);
    }

    /**
     * @psalm-api
     * @psalm-suppress UnusedParam
     *
     * @param string $buffer
     * @return void
     */
    private function initialize(string $buffer)
    {
        $this->setPosition(0);
        $this->setCols(0);
        $this->setToken(null);
        $this->setBuffer($buffer);
        $this->setScoped(false);
        $this->setArrayScoped(false);
        $this->setScopeCount(0);
        $this->setArrayScopeCount(0);
        $this->setTokens([]);
        $this->setNewlines(1);
        $this->setStrstart(null);
        $this->setReservedKeywords(['true', 'false']);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * {@inheritDoc}
     */
    public function decrementPosition(): void
    {
        $this->position--;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementPosition(): void
    {
        $this->position++;
    }
    /**
     * {@inheritdoc}
     */
    public function getCols(): int
    {
        return $this->cols;
    }

    /**
     * {@inheritdoc}
     */
    public function setCols(int $cols): void
    {
        $this->cols = $cols;
    }

    /**
     * {@inheritdoc}
     */
    public function resetCols(): void
    {
        $this->setCols(0);
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(): string|null
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(string|null $token): void
    {
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getBuffer(): string
    {
        return $this->buffer;
    }

    /**
     * {@inheritdoc}
     */
    public function setBuffer(string $buffer): void
    {
        $this->buffer = $buffer;
    }

    /**
     * {@inheritdoc}
     */
    public function getScoped(): bool
    {
        return $this->scoped;
    }

    /**
     * {@inheritdoc}
     */
    public function setScoped(bool $scoped): void
    {
        $this->scoped = $scoped;
    }

    /**
     * {@inheritdoc}
     */
    public function getArrayScoped(): bool
    {
        return $this->arrayScoped;
    }

    /**
     * {@inheritdoc}
     */
    public function setArrayScoped(bool $arrayScoped): void
    {
        $this->arrayScoped = $arrayScoped;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopeCount(): int
    {
        return $this->scopeCount;
    }

    /**
     * {@inheritdoc}
     */
    public function setScopeCount(int $scopeCount): void
    {
        $this->scopeCount = $scopeCount;
    }

    /**
     * {@inheritdoc}
     */
    public function setArrayScopeCount(int $arrayScopeCount): void
    {
        $this->arrayScopeCount = $arrayScopeCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokens(array $tokens): void
    {
        $this->tokens = $tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function addNode(NodeInterface $node): void
    {
        $this->tokens[] = $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewlines(): int
    {
        return $this->newlines;
    }

    /**
     * {@inheritdoc}
     */
    public function setNewlines(int $newlines): void
    {
        $this->newlines = $newlines;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementNewlines(): void
    {
        $this->newlines++;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementCols(): void
    {
        $this->cols++;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementScopeCount(): void
    {
        $this->scopeCount++;
    }

    /**
     * {@inheritdoc}
     */
    public function decrementScopeCount(): void
    {
        $this->scopeCount--;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementArrayScopeCount(): void
    {
        $this->arrayScopeCount++;
    }

    /**
     * {@inheritdoc}
     */
    public function getStrstart(): string|null
    {
        return $this->strstart;
    }

    /**
     * {@inheritdoc}
     */
    public function setStrstart(string|null $strstart): void
    {
        $this->strstart = $strstart;
    }

    /**
     * {@inheritdoc}
     */
    public function getReservedKeywords(): array
    {
        return $this->reservedKeywords;
    }

    /**
     * {@inheritdoc}
     */
    public function setReservedKeywords(array $keywords): void
    {
        $this->reservedKeywords = $keywords;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeAt(int $index): NodeInterface|null
    {
        return !isset($this->tokens[$index])
            ? null
            : $this->tokens[$index];
    }

    /**
     * {@inheritdoc}
     */
    public function getLastNode(): NodeInterface|null
    {
        return $this->getNodeAt(sizeof($this->tokens) - 1);
    }

    /**
     * {@inheritdoc}
     */
    public function lex(): void
    {
        while (true) {
            if ($this->isEof()) {
                $this->processWhenEof();
                break;
            }

            if ($this->isSpace() || $this->isTab()) {
                $this->incrementCols();
                $this->resetToken();
            }

            if ($this->isNewline()) {
                $this->processNewline();
                $this->resetToken();
            }

            if ($this->isStartCline()) {
                $this->incrementCols();
                $this->processCline();
                $this->resetToken();
            }

            if ($this->isOsb()) {
                $this->incrementCols();
                $this->processArrayOrBlock();
                $this->resetToken();
            }

            if ($this->isCsb()) {
                $this->incrementCols();
                $this->processEndblock();
                $this->resetToken();
            }

            if ($this->isDigits()) {
                $this->incrementCols();
                $this->processInteger();
                $this->resetToken();
            }

            if ($this->isSquote() || $this->isDquote()) {
                $this->incrementCols();
                $this->processString();
                $this->resetToken();
            }

            if ($this->isAssignment()) {
                $this->incrementCols();
                $this->processAssignment();
                $this->resetToken();
            }

            if ($this->isValidIdentAndBlockCompl()) {
                $this->incrementCols();
                $this->processIdentifier();
                $this->resetToken();
            }

            $this->persist();
            $this->next();
        }
    }

    /**
     * @return void
     */
    private function processWhenEof(): void
    {
        if ($this->getScoped() || $this->getArrayScoped()) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: unterminated block identifier",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }
    }

    /**
     * @return void
     */
    private function processCline(): void
    {
        while (true) {
            if ($this->isEof()) {
                break;
            }

            if ($this->isNewline()) {
                $this->persist();
                break;
            }

            $this->incrementCols();
            $this->next();
        }

        if ($this->isNewline()) {
            $this->processNewline();
        }
    }

    /**
     * @return void
     */
    private function processNewline(): void
    {
        $this->incrementNewlines();
        $this->resetCols();
    }

    /**
     * @return void
     */
    private function processAssignment(): void
    {
        /** @psalm-suppress PossiblyNullReference */
        if (
            count($this->tokens) == 0 ||
            $this->getLastNode()->getType() !== NodeTypes::IDENTIFIER
        ) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: assignment operator must be " .
                    "preceded by identifier.",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }

        $node = new Assign(
            $this->current(),
            $this->getCols(),
            $this->getNewlines()
        );

        $this->addNode($node);
    }

    /**
     * @return void
     */
    private function processArrayOrBlock(): void
    {
        /** @psalm-suppress PossiblyNullReference */
        if (
            sizeof($this->tokens) != 0 &&
            $this->getLastNode()->getType() == NodeTypes::ASSIGN
        ) {
            $this->processArray();
            return;
        }

        $this->processBlock();
    }

    /**
     * @return void
     */
    private function processArray(): void
    {
        if ($this->isEof()) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: unterminated array block identifier.",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }

        $this->incrementCols();
        $this->setArrayScoped(true);
        $this->incrementArrayScopeCount();
        $this->persist();

        $res = [];

        if ($this->isCsb()) {
            $node = new ArrayNode(
                $res,
                $this->getCols(),
                $this->getNewlines()
            );

            $this->processEndblock();
            $this->addNode($node);
            return;
        }

        $skipped = false;
        $commas  = 0;

        while (!$this->isCsb() && !$this->isEof()) {
            if ($this->isComma() || $this->isWhitespace()) {
                if ($this->isComma() && $commas > 0) {
                    throw new ConfigLexerException(
                        sprintf(
                            "Line %d, column %d: consecutive comma is not allowed.",
                            $this->getNewlines(),
                            $this->getCols() - 1
                        )
                    );
                }

                if ($this->isComma() && $commas == 0) {
                    $commas++;
                }

                $this->persist();
                $this->next();

                $skipped = true;
                continue;
            }

            if ($this->isValidIdentAndBlockAlpha()) {
                throw new ConfigLexerException(
                    sprintf(
                        "Line %d, column %d: array literal must not contain identifier.",
                        $this->getNewlines(),
                        $this->getCols() - 1
                    )
                );
            }

            if ($this->isSquote() || $this->isDquote()) {
                if (!$skipped) {
                    $this->next();
                }

                $res[]  = $this->processStringGeneric();
                $commas = $commas > 0 ? $commas - 1 : $commas;
                $this->resetToken();
            }

            if ($this->isDigits()) {
                if (!$skipped) {
                    $this->next();
                }

                $res[]  = $this->processIntegerGeneric();
                $commas = $commas > 0 ? $commas - 1 : $commas;
                $this->resetToken();
            }

            $skipped = false;

            $this->persist();

            if ($this->peek(1) !== ',') {
                $this->next();
            }
        }

        if (!$this->isCsb()) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: unterminated array literal.",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }

        if ($commas > 0) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: trailing comma before closing square brace is not allowed.",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }

        $this->setArrayScoped(false);
        $this->next();

        $node = new ArrayNode(
            $res,
            $this->getCols(),
            $this->getNewlines()
        );

        $this->addNode($node);
    }

    /**
     * @return void
     */
    private function processBlock(): void
    {
        if ($this->isEof()) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: unterminated block identifier.",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }

        $this->incrementCols();
        $this->setScoped(true);
        $this->incrementScopeCount();
        $this->persist();

        if ($this->isDigits()) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: block identifier cannot started with digit.",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }

        if (!$this->isValidIdentAndBlockCompl()) {
            /** @psalm-suppress PossiblyNullArgument */
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: block identifier contains " .
                    "invalid character (%s)",
                    $this->getNewlines(),
                    $this->getCols() - 1,
                    $this->current()
                )
            );
        }

        $res = $this->current();

        $this->next();

        while (true) {
            if ($this->isEof()) {
                break;
            }

            $this->incrementCols();
            $this->persist();

            if (!$this->isValidIdentAndBlockCompl()) {
                break;
            }

            /** @psalm-suppress PossiblyNullOperand */
            $res .= $this->current();
            $this->next();
        }

        $node = new Block(
            $res,
            $this->getCols(),
            $this->getNewlines()
        );

        $this->addNode($node);
    }

    /**
     * @return void
     */
    private function processEndblock(): void
    {
        $this->decrementScopeCount();

        if ($this->getScopeCount() == 0) {
            $this->setScoped(false);
        }
    }

    /**
     * @return int
     */
    private function processIntegerGeneric(): int
    {
        /** @psalm-suppress PossiblyNullReference */
        if (
            sizeof($this->tokens) == 0 ||
            $this->getLastNode()->getType() !== NodeTypes::ASSIGN &&
            !$this->getArrayScoped()
        ) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: integer literal " .
                    "must be preceded by assignment operator " .
                    " or must inside an array.",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }

        $res = $this->current();

        if ($this->isEof()) {
            return intval($res);
        }

        while (true) {
            if ($this->isEof()) {
                break;
            }

            $this->incrementCols();
            $this->persist();

            if (
                $this->isNewline() ||
                ($this->isCsb() && $this->getArrayScoped()) ||
                ($this->isComma() && $this->getArrayScoped())
            ) {
                break;
            }

            if (!$this->isNewline() && !$this->isDigits()) {
                /** @psalm-suppress PossiblyNullArgument */
                throw new ConfigLexerException(
                    sprintf(
                        "Line %d, column %d: integer literal must composed by digits (got: %s).",
                        $this->getNewlines(),
                        $this->getCols() - 1,
                        $this->current()
                    )
                );
            }

            /** @psalm-suppress PossiblyNullOperand */
            $res .= $this->current();
            $this->next();
        }

        return intval($res);
    }

    /**
     * @return void
     */
    private function processInteger(): void
    {
        try {
            $res = $this->processIntegerGeneric();
        } catch (ConfigLexerException $e) {
            throw $e;
        }

        $node = new Integer(
            intval($res),
            $this->getCols(),
            $this->getNewlines()
        );

        $this->addNode($node);
    }

    /**
     * @return string|null
     */
    private function processStringGeneric(): ?string
    {
        /** @psalm-suppress PossiblyNullReference */
        if (
            sizeof($this->tokens) === 0 ||
            ($this->getLastNode()->getType() !== NodeTypes::ASSIGN &&
             $this->getArrayScoped() === false)
        ) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: string literal " .
                    "must be preceded by assignment operator " .
                    "or must inside an array.",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }

        if ($this->isEof()) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: unterminated string literal.",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }

        $this->incrementCols();
        $this->setStrstart($this->current());
        $this->persist();

        if ($this->current() == $this->getStrstart()) {
            $this->next();
            $this->setStrstart(null);
            return '';
        }

        $buf = $this->current();

        $this->next();

        while (true) {
            if ($this->isEof()) {
                break;
            }

            $this->incrementCols();
            $this->persist();

            /*if (
                ($this->isNewline() || $this->isComma()) &&
                ($this->backtrack(1) !== $this->getStrstart())
            ) {
                var_dump($this->backtrack(1));
                throw new ConfigLexerException(
                    sprintf(
                        "Line %d, column %d: unterminated string literal.",
                        $this->getNewlines(),
                        $this->getCols() - 1
                    )
                );
            }*/

            if (
                $this->current() === $this->getStrstart() &&
                $this->backtrack(1) !== "\\"
            ) {
                break;
            }

            /** @psalm-suppress PossiblyNullOperand */
            $buf .= $this->current();
            $this->next();
        }

        if ($this->current() !== $this->getStrstart()) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: unterminated string literal.",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }

        if (
            $this->peek(1) !== ',' &&
            $this->peek(1) !== ']' &&
            $this->getArrayScoped()
        ) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: every array elements must be " .
                    "separated by comma.",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }

        $this->next();
        $this->setStrstart(null);

        return $buf;
    }

    /**
     * @return void
     */
    private function processString(): void
    {
        try {
            $node = new Str(
                $this->processStringGeneric(),
                $this->getCols(),
                $this->getNewlines()
            );

            $this->addNode($node);
        } catch (ConfigLexerException $e) {
            throw $e;
        }
    }

    /**
     * @return void
     */
    private function processIdentifier(): void
    {
        /** @psalm-suppress PossiblyNullReference */
        if (
            sizeof($this->tokens) === 0 ||
            ($this->getLastNode()->getType() !== NodeTypes::BLOCK &&
             $this->getLastNode()->getType() !== NodeTypes::BOOLEAN &&
             $this->getLastNode()->getType() !== NodeTypes::STRING &&
             $this->getLastNode()->getType() !== NodeTypes::INTEGER &&
             $this->getLastNode()->getType() !== NodeTypes::ARRAY &&
             $this->getLastNode()->getType() !== NodeTypes::ASSIGN)
        ) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: identifier must be preceded by " .
                    "block, string, integer, array, or assignment " .
                    "identifier.",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }

        if ($this->isEof()) {
            return;
        }

        $buf = $this->current();

        while (true) {
            if ($this->isEof()) {
                break;
            }

            $this->incrementCols();
            $this->persist();

            if (!$this->isValidIdentAndBlockCompl()) {
                break;
            }

            /** @psalm-suppress PossiblyNullOperand */
            $buf .= $this->current();
            $this->next();
        }

        /** @psalm-suppress PossiblyNullReference */
        if (
            $this->getLastNode()->getType() === NodeTypes::ASSIGN &&
            ($buf !== 'true' && $buf !== 'false')
        ) {
            throw new ConfigLexerException(
                sprintf(
                    "Line %d, column %d: only boolean types that can " .
                    "followed by assignment operator.",
                    $this->getNewlines(),
                    $this->getCols() - 1
                )
            );
        }

        /** @psalm-suppress PossiblyNullReference */
        if (
            $this->getLastNode()->getType() === NodeTypes::ASSIGN &&
            ($buf === 'true' || $buf === 'false')
        ) {
            $node = new Boolean(
                $buf === 'true' ? true : false,
                $this->getCols(),
                $this->getNewlines()
            );

            $this->addNode($node);
            return;
        }

        $node = new Identifier(
            $buf,
            $this->getCols(),
            $this->getNewlines()
        );

        $this->addNode($node);
    }

    /**
     * @return bool
     */
    private function isOsb(): bool
    {
        return $this->current() == '[';
    }

    /**
     * @return bool
     */
    private function isCsb(): bool
    {
        return $this->current() == ']';
    }

    /**
     * @return bool
     */
    private function isSpace(): bool
    {
        return $this->current() == ' ';
    }

    /**
     * @return bool
     */
    private function isTab(): bool
    {
        return $this->current() == "\t";
    }

    /**
     * @return bool
     */
    private function isNewline(): bool
    {
        return $this->current() == "\n";
    }

    /**
     * @return bool
     */
    private function isStartCline(): bool
    {
        return $this->current() == '#';
    }

    /**
     * @return bool
     */
    private function isSquote(): bool
    {
        return $this->current() == '\'';
    }

    /**
     * @return bool
     */
    private function isDquote(): bool
    {
        return $this->current() == '"';
    }

    /**
     * @return bool
     */
    private function isEof(): bool
    {
        return $this->getPosition() >= strlen($this->getBuffer());
    }

    /**
     * @return bool
     */
    private function isComma(): bool
    {
        return $this->current() == ',';
    }

    /**
     * @return bool
     */
    private function isWhitespace(): bool
    {
        return $this->isSpace() || $this->isTab() || $this->isNewline();
    }

    /**
     * @return void
     */
    private function resetToken(): void
    {
        $this->setToken(null);
    }

    /**
     * @return void
     */
    private function next(): void
    {
        $this->incrementPosition();
    }

    /**
     * @psalm-api
     * @psalm-suppress UnusedParam
     *
     * @return string|null
     */
    private function peek(int $depth): string|null
    {
        if ($this->getPosition() + $depth >= strlen($this->getBuffer())) {
            return null;
        }

        return $this->buffer[$this->getPosition() + $depth];
    }

    /**
     * @psalm-api
     * @psalm-suppress UnusedParam
     *
     * @return string|null
     */
    private function backtrack(int $depth): string|null
    {
        if ($this->getPosition() - $depth < 0) {
            return null;
        }

        return $this->buffer[$this->getPosition() - $depth];
    }

    /**
     * @return string|null
     */
    private function current(): string|null
    {
        return $this->getToken();
    }

    /**
     * @return void
     */
    private function persist(): void
    {
        if ($this->isEof()) {
            return;
        }

        $this->setToken($this->buffer[$this->getPosition()]);
    }

    /**
     * @return bool
     */
    private function isDigits(): bool
    {
        return $this->current() === null
            ? false
            : is_numeric($this->current());
    }

    /**
     * @return bool
     */
    private function isValidIdentAndBlockAlpha(): bool
    {
        /** @psalm-suppress PossiblyNullArgument */
        return $this->current() === null
            ? false
            : ctype_alpha($this->current());
    }

    /**
     * @return bool
     */
    private function isValidIdentAndBlockCompl(): bool
    {
        if ($this->current() === null) {
            return false;
        }

        /** @psalm-suppress PossiblyNullArgument */
        return ctype_alnum($this->current()) ||
               $this->current() === '-' ||
               $this->current() === '_';
    }

    /**
     * @return bool
     */
    private function isAssignment(): bool
    {
        if ($this->current() === null) {
            return false;
        }

        return $this->current() === '=';
    }
}
