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
class VolunteerBulkResponseState extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'index')]
    #[OpenApi\Property(
        property: 'index',
        type: 'integer',
        description: 'Volunteer state index'
    )]
    private int $index;

    /**
     * @var string
     */
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Volunteer state ID'
    )]
    private string $id;

    /**
     * @var int
     */
    #[Json(name: 'status')]
    #[OpenApi\Property(
        property: 'status',
        type: 'integer',
        description: 'Volunteer status HTTP response code'
    )]
    private int $status;

    /**
     * @var string
     */
    #[Json(name: 'reason')]
    #[OpenApi\Property(
        property: 'reason',
        type: 'string',
        description: 'Volunteer status HTTP response reason'
    )]
    private string $reason;

    /**
     * @var string
     */
    #[Json(name: 'message')]
    #[OpenApi\Property(
        property: 'message',
        type: 'string',
        description: 'Volunteer creation status message'
    )]
    private string $message;

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
     * @return string
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
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     * @return void
     */
    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
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
