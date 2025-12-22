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
class NuCoinResponseState extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'index')]
    #[OpenApi\Property(
        property: 'index',
        type: 'integer',
        description: 'NU coin state index'
    )]
    private int $index;

    /**
     * @var string
     */
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'NU coin response state ID'
    )]
    private string $id;

    /**
     * @var int
     */
    #[Json(name: 'status')]
    #[OpenApi\Property(
        property: 'status',
        type: 'integer',
        description: 'NU coin response state status'
    )]
    private int $status;

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @param int $index
     * @return void
     */
    public function setIndex(int $index): void
    {
        $this->index = $index;
    }

    /**
     * @return status
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return void
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return void
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
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
