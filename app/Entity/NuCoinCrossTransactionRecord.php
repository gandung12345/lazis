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
#[Entity, Table(name: 'nuCoinCrossTransactionRecord')]
#[HasLifecycleCallbacks]
#[OpenApi\Schema]
class NuCoinCrossTransactionRecord extends AbstractEntity
{
    /**
     * @var string
     */
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'NU coin cross transaction record ID',
        readOnly: true
    )]
    private string $id;

    /**
     * @var \Schnell\Decorator\Stringified\DateTimeDecorator
     */
    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'date')]
    #[OpenApi\Property(
        property: 'date',
        type: 'string',
        format: 'date',
        description: 'NU coin cross transaction record'
    )]
    private DateTimeDecorator $date;

    /**
     * @var string
     */
    #[Column(type: 'guid', nullable: false)]
    #[Json(name: 'sourceId')]
    #[OpenApi\Property(
        property: 'sourceId',
        type: 'string',
        description: 'NU coin cross transaction record source ID'
    )]
    private string $sourceId;

    /**
     * @var string
     */
    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'sourceName')]
    #[OpenApi\Property(
        property: 'sourceName',
        type: 'string',
        description: 'NU coin cross transaction record source name'
    )]
    private string $sourceName;

    /**
     * @var string
     */
    #[Column(type: 'guid', nullable: false)]
    #[Json(name: 'destinationId')]
    #[OpenApi\Property(
        property: 'destinationId',
        type: 'string',
        description: 'NU coin cross transaction record destination ID'
    )]
    private string $destinationId;

    /**
     * @var string
     */
    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'destinationName')]
    #[OpenApi\Property(
        property: 'destinationName',
        type: 'string',
        description: 'NU coin cross transaction record destination name'
    )]
    private string $destinationName;

    /**
     * @var int
     */
    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'status')]
    #[OpenApi\Property(
        property: 'status',
        type: 'integer',
        description: 'NU coin cross transaction record status'
    )]
    private int $status;

    /**
     * @var int
     */
    #[Column(name: 'integer', nullable: false)]
    #[Json(name: 'type')]
    #[OpenApi\Property(
        property: 'type',
        type: 'integer',
        description: 'NU coin cross transaction record type'
    )]
    private int $type;

    /**
     * @var int
     */
    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'amount')]
    #[OpenApi\Property(
        property: 'amount',
        type: 'integer',
        description: 'NU coin cross transaction record amount'
    )]
    private int $amount;

    /**
     * @var string
     */
    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'proof')]
    #[OpenApi\Property(
        property: 'proof',
        type: 'string',
        description: 'NU coin cross transaction record proof'
    )]
    private string $proof;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Json(name: 'createdAt')]
    #[OpenApi\Property(
        property: 'createdAt',
        type: 'timestamp',
        description: 'NU coin cross transaction created at'
    )]
    private ?DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Json(name: 'updatedAt')]
    #[OpenApi\Property(
        property: 'updatedAt',
        type: 'timestamp',
        description: 'NU coin cross transaction updated at'
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
    public function getDate(): DateTimeDecorator
    {
        return $this->date;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator $date
     * @return void
     */
    public function setDate(DateTimeDecorator $date): void
    {
        $this->date = $date;
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
     * @return string
     */
    public function getProof(): string
    {
        return $this->proof;
    }

    /**
     * @param string $proof
     * @return void
     */
    public function setProof(string $proof): void
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
        return '__nuCoinCrossTransactionRecord__';
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
        return NuCoinCrossTransactionRecord::class;
    }
}
