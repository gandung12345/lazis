<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'nuCoinAggregator')]
#[HasLifecycleCallbacks]
#[OpenApi\Schema]
class NuCoinAggregator extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'NU coin aggregator ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'date')]
    #[OpenApi\Property(
        property: 'date',
        type: 'string',
        format: 'date',
        description: 'NU coin aggregator date'
    )]
    private DateTimeDecorator $date;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'amount')]
    #[OpenApi\Property(
        property: 'amount',
        type: 'integer',
        description: 'NU coin aggregator amount'
    )]
    private int $amount;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Json(name: 'createdAt')]
    #[OpenApi\Property(
        property: 'createdAt',
        type: 'timestamp',
        description: 'NU coin aggregator created at'
    )]
    private ?DateTime $createdAt;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Json(name: 'updatedAt')]
    #[OpenApi\Property(
        property: 'updatedAt',
        type: 'timestamp',
        description: 'NU coin aggregator updated at'
    )]
    private ?DateTime $updatedAt;

    #[ManyToOne(targetEntity: Donor::class, inversedBy: 'nuCoinAggregators')]
    #[JoinColumn(name: 'donorRefId', referencedColumnName: 'id')]
    private Donor $donor;

    #[OneToOne(
        targetEntity: Transaction::class,
        mappedBy: 'nuCoinAggregator',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Transaction $transaction;

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
     * @return \Lazis\Api\Entity\Donor
     */
    public function getDonor(): Donor
    {
        return $this->donor;
    }

    /**
     * @param \Lazis\Api\Entity\Donor $donor
     * @return void
     */
    public function setDonor(Donor $donor): void
    {
        $this->donor = $donor;
    }

    /**
     * @return \Lazis\Api\Entity\Transaction
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }

    /**
     * @param \Lazis\Api\Entity\Transaction $transaction
     * @return void
     */
    public function setTransaction(Transaction $transaction): void
    {
        $this->transaction = $transaction;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__nuCoinAggregator__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'nuCoinAggregator';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return NuCoinAggregator::class;
    }
}
