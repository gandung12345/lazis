<?php

declare(strict_types=1);

namespace Schnell\Http\FQL;

use Schnell\Exception\FQLLexerException;
use Schnell\Http\FQL\Node\NodeInterface;
use Schnell\Http\FQL\Node\Expr\AndX;
use Schnell\Http\FQL\Node\Expr\EqualX;
use Schnell\Http\FQL\Node\Expr\GreaterOrEqualX;
use Schnell\Http\FQL\Node\Expr\GreaterX;
use Schnell\Http\FQL\Node\Expr\LessOrEqualX;
use Schnell\Http\FQL\Node\Expr\LessX;
use Schnell\Http\FQL\Node\Expr\LikeX;
use Schnell\Http\FQL\Node\Expr\NotEqualX;
use Schnell\Http\FQL\Node\Expr\OrX;
use Schnell\Http\FQL\Node\Literal\Integer;
use Schnell\Http\FQL\Node\Literal\Str;
use Schnell\Http\FQL\Node\Symbol\CloseSquareBrace;
use Schnell\Http\FQL\Node\Symbol\Colon;
use Schnell\Http\FQL\Node\Symbol\Comma;
use Schnell\Http\FQL\Node\Symbol\OpenSquareBrace;

use function is_numeric;
use function strlen;

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Lexer implements LexerInterface
{
    /**
     * @var string
     */
    private ?string $buffer;

    /**
     * @var int
     */
    private int $position;

    /**
     * @var array
     */
    private array $tokens;

    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var string|null
     */
    private ?string $token;

    /**
     * @readonly
     * @psalm-allow-private-mutation
     *
     * @var string|null
     */
    private ?string $strstart;

    /**
     * @psalm-api
     *
     * @param string|null $buffer
     * @return static
     */
    public function __construct(?string $buffer = null)
    {
        $this->setBuffer($buffer);
        $this->setPosition(0);
        $this->setToken(null);
        $this->setTokens([]);
    }

    /**
     * {@inheritdoc}
     */
    public function lex(): void
    {
        while (true) {
            if ($this->isEof()) {
                $this->tokenizeWhenEof();
                break;
            }

            if ($this->isDigits()) {
                $this->tokenizeIntegerLiteral();
                $this->drop();
            }

            if (
                $this->isSingleQuote() ||
                $this->isDoubleQuote()
            ) {
                $this->tokenizeStringLiteral();
                $this->drop();
            }

            if ($this->isOpenSquareBrace()) {
                $this->tokenizeOpenSquareBrace();
                $this->drop();
            }

            if ($this->isCloseSquareBrace()) {
                $this->tokenizeCloseSquareBrace();
                $this->drop();
            }

            if ($this->isColon()) {
                $this->tokenizeColon();
                $this->drop();
            }

            if ($this->isComma()) {
                $this->tokenizeComma();
                $this->drop();
            }

            if ($this->isDollar()) {
                $this->tokenizeExpr();
                $this->drop();
            }

            $this->persist();
            $this->next();
        }
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
    public function addToken(NodeInterface $node): void
    {
        $this->tokens[] = $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getBuffer(): ?string
    {
        return $this->buffer;
    }

    /**
     * {@inheritdoc}
     */
    public function setBuffer(?string $buffer): void
    {
        $this->buffer = $buffer;
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
     * {@inheritdoc}
     */
    public function incrementPosition(): void
    {
        $this->position++;
    }

    /**
     * @return string|null
     */
    private function getStrstart(): ?string
    {
        return $this->strstart;
    }

    /**
     * @psalm-api
     * @psalm-suppress PossiblyUnusedParam
     * @psalm-suppress UnusedParam
     *
     * @param string|null $strstart
     * @return void
     */
    private function setStrstart(?string $strstart): void
    {
        $this->strstart = $strstart;
    }

    /**
     * @return string|null
     */
    private function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @psalm-api
     * @psalm-suppress UnusedParam
     *
     * @param string|null $token
     * @return void
     */
    private function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return bool
     */
    private function isEof(): bool
    {
        /** @psalm-suppress PossiblyNullArgument */
        return $this->getPosition() >= strlen($this->getBuffer());
    }

    /**
     * @return void
     */
    private function persist(): void
    {
        /** @psalm-suppress PossiblyNullArrayAccess */
        $this->setToken($this->buffer[$this->getPosition()]);
    }

    /**
     * @return void
     */
    private function drop(): void
    {
        $this->setToken(null);
    }

    /**
     * @return string|null
     */
    private function current(): ?string
    {
        return $this->getToken();
    }

    /**
     * @return bool
     */
    private function isSingleQuote(): bool
    {
        return $this->current() === "'";
    }

    /**
     * @return bool
     */
    private function isDoubleQuote(): bool
    {
        return $this->current() === '"';
    }

    /**
     * @return bool
     */
    private function isOpenSquareBrace(): bool
    {
        return $this->current() === '[';
    }

    /**
     * @return bool
     */
    private function isCloseSquareBrace(): bool
    {
        return $this->current() === ']';
    }

    /**
     * @return bool
     */
    private function isComma(): bool
    {
        return $this->current() === ',';
    }

    /**
     * @return bool
     */
    private function isColon(): bool
    {
        return $this->current() === ':';
    }

    /**
     * @return bool
     */
    private function isDollar(): bool
    {
        return $this->current() === '$';
    }

    /**
     * @return bool
     */
    private function isDigits(): bool
    {
        /** @psalm-suppress PossiblyNullArgument */
        return is_numeric($this->current());
    }

    /**
     * @return bool
     */
    private function isAlphaLowercase(): bool
    {
        /** @psalm-suppress PossiblyNullArgument */
        return ctype_lower($this->current());
    }

    /**
     * @return void
     */
    private function tokenizeCloseSquareBrace(): void
    {
        $this->addToken(new CloseSquareBrace());
    }

    /**
     * @return void
     */
    private function tokenizeOpenSquareBrace(): void
    {
        $this->addToken(new OpenSquareBrace());
    }

    /**
     * @return void
     */
    private function tokenizeColon(): void
    {
        $this->addToken(new Colon());
    }

    /**
     * @return void
     */
    private function tokenizeComma(): void
    {
        $this->addToken(new Comma());
    }

    /**
     * @return void
     */
    private function tokenizeIntegerLiteral(): void
    {
        $buf = $this->current();

        while (true) {
            if ($this->isEof()) {
                break;
            }

            $this->persist();

            if (!$this->isDigits()) {
                break;
            }

            /** @psalm-suppress PossiblyNullOperand */
            $buf .= $this->current();
            $this->next();
        }

        $this->addToken(new Integer(intval($buf)));
    }

    /**
     * @return void
     */
    private function tokenizeStringLiteral(): void
    {
        $this->setStrstart($this->current());
        $this->persist();

        if ($this->current() === $this->getStrstart()) {
            $this->setStrstart(null);
            $this->addToken(new Str(''));
            $this->next();
            return;
        }

        $buf = $this->current();
        $valid = false;

        $this->next();

        while (true) {
            if ($this->isEof()) {
                break;
            }

            $this->persist();

            if (
                ($this->current() === $this->getStrstart()) &&
                ($this->backtrack() !== '\\')
            ) {
                $valid = true;
                break;
            }

            /** @psalm-suppress PossiblyNullOperand */
            $buf .= $this->current();
            $this->next();
        }

        if (!$valid) {
            throw new FQLLexerException("Unterminated string literal.");
        }

        $this->setStrstart(null);
        $this->addToken(new Str($buf));
        $this->next();
    }

    /**
     * @return void
     */
    private function tokenizeExpr(): void
    {
        $this->persist();

        if (!$this->isAlphaLowercase()) {
            return;
        }

        $buf = $this->current();

        /** @psalm-suppress PossiblyNullArgument */
        if ($this->checkAndPersistExpr($buf)) {
            return;
        }

        $this->next();

        while (true) {
            if ($this->isEof()) {
                break;
            }

            $this->persist();

            if (!$this->isAlphaLowercase()) {
                break;
            }

            /** @psalm-suppress PossiblyNullOperand */
            $buf .= $this->current();

            if ($this->checkAndPersistExpr($buf)) {
                return;
            }

            $this->next();
        }

        return;
    }

    /**
     * @return void
     */
    private function tokenizeWhenEof(): void
    {
        if ($this->isCloseSquareBrace()) {
            $this->tokenizeCloseSquareBrace();
        }

        return;
    }

    /**
     * @psalm-api
     * @psalm-suppress PossiblyUnusedParam
     *
     * @param string $buf
     * @return bool
     */
    private function checkAndPersistExpr(string $buf): bool
    {
        switch ($buf) {
            case 'and':
                $this->addToken(new AndX());
                break;
            case 'eq':
                $this->addToken(new EqualX());
                break;
            case 'gt':
                $this->addToken(new GreaterX());
                break;
            case 'gte':
                $this->addToken(new GreaterOrEqualX());
                break;
            case 'like':
                $this->addToken(new LikeX());
                break;
            case 'lt':
                $this->addToken(new LessX());
                break;
            case 'lte':
                $this->addToken(new LessOrEqualX());
                break;
            case 'neq':
                $this->addToken(new NotEqualX());
                break;
            case 'or':
                $this->addToken(new OrX());
                break;
            default:
                return false;
        }

        return true;
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
     * @param int $depth
     * @return string|null
     */
    private function peek(int $depth = 1): string|null
    {
        /** @psalm-suppress PossiblyNullArgument */
        if ($this->getPosition() + $depth >= strlen($this->getBuffer())) {
            return null;
        }

        /** @psalm-suppress PossiblyNullArrayAccess */
        return $this->buffer[$this->getPosition() + $depth];
    }

    /**
     * @psalm-api
     * @psalm-suppress PossiblyUnusedParam
     *
     * @param int $depth
     * @return string|null
     */
    private function backtrack(int $depth = 1): string|null
    {
        if ($this->getPosition() - $depth < 0) {
            return null;
        }

        /** @psalm-suppress PossiblyNullArrayAccess */
        return $this->buffer[$this->getPosition() - $depth];
    }
}
