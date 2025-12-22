<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'zakat')]
#[OpenApi\Schema]
class Zakat extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Zakat ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'date')]
    #[OpenApi\Property(
        property: 'date',
        type: 'string',
        format: 'date',
        description: 'Zakat issued date'
    )]
    private DateTimeDecorator $date;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'muzakki')]
    #[OpenApi\Property(
        property: 'muzakki',
        type: 'integer',
        description: 'Zakat muzakki type'
    )]
    private int $muzakki;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'name')]
    #[OpenApi\Property(
        property: 'name',
        type: 'string',
        description: 'Zakat aggregator name'
    )]
    private string $name;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'npwz')]
    #[OpenApi\Property(
        property: 'npwz',
        type: 'string',
        description: 'Zakat aggregator npwz'
    )]
    private string $npwz;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'npwp')]
    #[OpenApi\Property(
        property: 'npwp',
        type: 'string',
        description: 'Zakat aggregator npwp'
    )]
    private string $npwp;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'address')]
    #[OpenApi\Property(
        property: 'address',
        type: 'string',
        description: 'Zakat aggregator address'
    )]
    private string $address;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'phoneNumber')]
    #[OpenApi\Property(
        property: 'phoneNumber',
        type: 'string',
        description: 'Zakat aggregator phone number'
    )]
    private string $phoneNumber;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'amount')]
    #[OpenApi\Property(
        property: 'amount',
        type: 'integer',
        description: 'Zakat aggregator amount'
    )]
    private int $amount;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'type')]
    #[OpenApi\Property(
        property: 'type',
        type: 'integer',
        description: 'Zakat type'
    )]
    private int $type;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'description')]
    #[OpenApi\Property(
        property: 'description',
        type: 'string',
        description: 'Zakat description'
    )]
    private string $description;

    #[ManyToOne(targetEntity: Amil::class, inversedBy: 'zakats')]
    #[JoinColumn(name: 'amilRefId', referencedColumnName: 'id')]
    private Amil $amil;

    #[OneToOne(
        targetEntity: Transaction::class,
        mappedBy: 'zakat',
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
    public function getMuzakki(): int
    {
        return $this->muzakki;
    }

    /**
     * @param int $muzakki
     * @return void
     */
    public function setMuzakki(int $muzakki): void
    {
        $this->muzakki = $muzakki;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getNpwz(): string
    {
        return $this->npwz;
    }

    /**
     * @param string $npwz
     * @return void
     */
    public function setNpwz(string $npwz): void
    {
        $this->npwz = $npwz;
    }

    /**
     * @return string
     */
    public function getNpwp(): string
    {
        return $this->npwp;
    }

    /**
     * @param string $npwp
     * @return void
     */
    public function setNpwp(string $npwp): void
    {
        $this->npwp = $npwp;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return void
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     * @return void
     */
    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return \Lazis\Api\Entity\Amil
     */
    public function getAmil(): Amil
    {
        return $this->amil;
    }

    /**
     * @param \Lazis\Api\Entity\Amil $amil
     * @return void
     */
    public function setAmil(Amil $amil): void
    {
        $this->amil = $amil;
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
        return '__zakat__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'zakat';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return Zakat::class;
    }
}
