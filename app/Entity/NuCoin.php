<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'nuCoin')]
#[OpenApi\Schema]
class NuCoin extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'NU coin ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'date')]
    #[OpenApi\Property(
        property: 'date',
        type: 'string',
        format: 'date',
        description: 'NU coin date'
    )]
    private DateTimeDecorator $date;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'amount')]
    #[OpenApi\Property(
        property: 'amount',
        type: 'integer',
        description: 'NU coin amount'
    )]
    private int $amount;

    #[ManyToOne(targetEntity: Donor::class, inversedBy: 'nuCoins')]
    #[JoinColumn(name: 'donorRefId', referencedColumnName: 'id')]
    private Donor $donor;

    #[OneToOne(
        targetEntity: Transaction::class,
        mappedBy: 'nuCoin',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Transaction $transaction;

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
        return '__nuCoin__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'nuCoin';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return NuCoin::class;
    }
}
