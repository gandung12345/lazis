<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use RuntimeException;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[OpenApi\Schema]
class NuCoinCrossTransferState extends AbstractEntity
{
    /**
     * @var string
     */
    #[Json(name: 'source')]
    #[OpenApi\Property(
        property: 'source',
        type: 'string',
        description: 'NU coin cross transfer state source'
    )]
    private string $source;

    /**
     * @var string
     */
    #[Json(name: 'destination')]
    #[OpenApi\Property(
        property: 'destination',
        type: 'string',
        description: 'NU coin cross transfer state destination'
    )]
    private string $destination;

    /**
     * @var string
     */
    #[Json(name: 'amount')]
    #[OpenApi\Property(
        property: 'amount',
        type: 'integer',
        description: 'NU coin cross transfer state amount'
    )]
    private int $amount;

    /**
     * @var int
     */
    #[Json(name: 'statusCode')]
    #[OpenApi\Property(
        property: 'statusCode',
        type: 'integer',
        description: 'NU coin cross transfer state HTTP status code'
    )]
    private int $statusCode;

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return void
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getDestination(): string
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     * @return void
     */
    public function setDestination(string $destination): void
    {
        $this->destination = $destination;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     * @return void
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return void
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        throw new RuntimeException("Not implemented");
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        throw new RuntimeException("Not implemented");
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        throw new RuntimeException("Not implemented");
    }
}
