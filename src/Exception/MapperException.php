<?php

declare(strict_types=1);

namespace Schnell\Exception;

use Exception;
use Schnell\Mapper\Query\Error as QueryError;

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class MapperException extends Exception
{
    /**
     * @var string
     */
    private ?string $sqlState;

    /**
     * @var string
     */
    private ?string $sqlStateDescription;

    /**
     * @psalm-api
     *
     * @param string $message
     * @param string|null $sqlState
     * @param string|null $sqlStateDescription
     * @return static
     */
    public function __construct(
        string $message,
        ?string $sqlState = null,
        ?string $sqlStateDescription = null
    ) {
        $this->setSqlState($sqlState);
        $this->setSqlStateDescription($sqlStateDescription);
        parent::__construct($message);
    }

    /**
     * @psalm-api
     *
     * @return string|null
     */
    public function getSqlState(): ?string
    {
        return $this->sqlState;
    }

    /**
     * @param string|null $sqlState
     * @return void
     */
    public function setSqlState(?string $sqlState): void
    {
        $this->sqlState = $sqlState;
    }

    /**
     * @psalm-api
     *
     * @return string|null
     */
    public function getSqlStateDescription(): ?string
    {
        return $this->sqlStateDescription;
    }

    /**
     * @param string|null $sqlStateDescription
     * @return void
     */
    public function setSqlStateDescription(?string $sqlStateDescription): void
    {
        $this->sqlStateDescription = $sqlStateDescription;
    }
}
