<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;
use Schnell\Decorator\Stringified\DateTimeDecorator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'nuCoinCrossTransferQueue')]
#[HasLifecycleCallbacks]
#[OpenApi\Schema]
class NuCoinCrossTransferQueue extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Cross transfer queue ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'issuedAt')]
    #[OpenApi\Property(
        property: 'issuedAt',
        type: 'string',
        format: 'date',
        description: 'Cross transfer queue issued at'
    )]
    private DateTimeDecorator $issuedAt;

    #[Column(type: 'guid', nullable: false)]
    #[Json(name: 'sourceId')]
    #[OpenApi\Property(
        property: 'sourceId',
        type: 'string',
        description: 'Cross transfer queue source ID'
    )]
    private string $sourceId;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'sourceName')]
    #[OpenApi\Property(
        property: 'sourceName',
        type: 'string',
        description: 'Cross transfer queue source name'
    )]
    private string $sourceName;

    #[Column(type: 'guid', nullable: false)]
    #[Json(name: 'destinationId')]
    #[OpenApi\Property(
        property: 'destinationId',
        type: 'string',
        description: 'Cross transfer queue destination ID'
    )]
    private string $destinationId;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'destinationName')]
    #[OpenApi\Property(
        property: 'destinationName',
        type: 'string',
        description: 'Cross transfer queue destination name'
    )]
    private string $destinationName;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'amount')]
    #[OpenApi\Property(
        property: 'amount',
        type: 'integer',
        description: 'Cross transfer queue amount'
    )]
    private int $amount;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'transferAmount')]
    #[OpenApi\Property(
        property: 'transferAmount',
        type: 'integer',
        description: 'Cross transfer queue transfer amount'
    )]
    private int $transferAmount;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'status')]
    #[OpenApi\Property(
        property: 'status',
        type: 'integer',
        description: 'Cross transfer queue status'
    )]
    private int $status;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'type')]
    #[OpenApi\Property(
        property: 'type',
        type: 'integer',
        description: 'Cross transfer queue type'
    )]
    private int $type;

    #[Column(type: 'text', nullable: true)]
    #[Json(name: 'proof')]
    #[OpenApi\Property(
        property: 'proof',
        type: 'string',
        description: 'Cross transfer queue proof'
    )]
    private ?string $proof;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Json(name: 'createdAt')]
    #[OpenApi\Property(
        property: 'createdAt',
        type: 'timestamp',
        description: 'Cross transfer queue created at'
    )]
    private ?DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Json(name: 'updatedAt')]
    #[OpenApi\Property(
        property: 'updatedAt',
        type: 'timestamp',
        description: 'Cross transfer queue updated at'
    )]
    private ?DateTime $updatedAt;

    #[PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->setCreatedAt(new DateTime());
        $this->setUpdatedAtValue();
    }

    #[PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->setUpdatedAt(new DateTime());
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
     * @return \Schnell\Decorator\Stringified\DateTimeDecorator
     */
    public function getIssuedAt(): DateTimeDecorator
    {
        return $this->issuedAt;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator $issuedAt
     * @return void
     */
    public function setIssuedAt(DateTimeDecorator $issuedAt): void
    {
        $this->issuedAt = $issuedAt;
    }

    /**
     * @return string
     */
    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    /**
     * @param string $sourceId
     * @return void
     */
    public function setSourceId(string $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    /**
     * @return string
     */
    public function getSourceName(): string
    {
        return $this->sourceName;
    }

    /**
     * @param string $sourceName
     * @return void
     */
    public function setSourceName(string $sourceName): void
    {
        $this->sourceName = $sourceName;
    }

    /**
     * @return string
     */
    public function getDestinationId(): string
    {
        return $this->destinationId;
    }

    /**
     * @param string $destinationId
     * @return void
     */
    public function setDestinationId(string $destinationId): void
    {
        $this->destinationId = $destinationId;
    }

    /**
     * @return string
     */
    public function getDestinationName(): string
    {
        return $this->destinationName;
    }

    /**
     * @param string $destinationName
     * @return void
     */
    public function setDestinationName(string $destinationName): void
    {
        $this->destinationName = $destinationName;
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
    public function getTransferAmount(): int
    {
        return $this->transferAmount;
    }

    /**
     * @param int $transferAmount
     * @return void
     */
    public function setTransferAmount(int $transferAmount): void
    {
        $this->transferAmount = $transferAmount;
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
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return void
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getProof(): ?string
    {
        return $this->proof;
    }

    /**
     * @param string|null $proof
     * @return void
     */
    public function setProof(?string $proof): void
    {
        $this->proof = $proof;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime|null $createdAt
     * @return void
     */
    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|null $updatedAt
     * @return void
     */
    public function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__nuCoinCrossTransferQueue__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'nuCoinCrossTransferQueue';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return NuCoinCrossTransferQueue::class;
    }
}
