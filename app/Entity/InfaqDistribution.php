<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'infaqDistribution')]
#[OpenApi\Schema]
class InfaqDistribution extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Infaq distribution ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'date')]
    #[OpenApi\Property(
        property: 'date',
        type: 'string',
        format: 'date',
        description: 'Infaq distribution date'
    )]
    private DateTimeDecorator $date;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'program')]
    #[OpenApi\Property(
        property: 'program',
        type: 'integer',
        description: 'Infaq distribution program'
    )]
    // picklist: ZakatDistribution
    private int $program;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'fundingResource')]
    #[OpenApi\Property(
        property: 'fundingResource',
        type: 'integer',
        description: 'Infaq distribution funding resource'
    )]
    // picklist: Wallet
    private int $fundingResource;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'receivingCategory')]
    #[OpenApi\Property(
        property: 'receivingCategory',
        type: 'integer',
        description: 'Infaq distribution receiving category'
    )]
    // picklist: Muzakki
    private int $receivingCategory;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'name')]
    #[OpenApi\Property(
        property: 'name',
        type: 'string',
        description: 'Infaq distribution name'
    )]
    private string $name;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'address')]
    #[OpenApi\Property(
        property: 'address',
        type: 'string',
        description: 'Infaq distribution address'
    )]
    private string $address;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'phoneNumber')]
    #[OpenApi\Property(
        property: 'phoneNumber',
        type: 'string',
        description: 'Infaq distribution phone number'
    )]
    private string $phoneNumber;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'numberOfBeneficiaries')]
    #[OpenApi\Property(
        property: 'numberOfBeneficiaries',
        type: 'integer',
        description: 'Infaq distribution number of beneficiaries'
    )]
    private int $numberOfBeneficiaries;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'amount')]
    #[OpenApi\Property(
        property: 'amount',
        type: 'integer',
        description: 'Infaq distribution amount'
    )]
    private int $amount;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'description')]
    #[OpenApi\Property(
        property: 'description',
        type: 'string',
        description: 'Infaq distribution description'
    )]
    private string $description;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'proof')]
    #[OpenApi\Property(
        property: 'proof',
        type: 'string',
        description: 'Infaq distribution proof'
    )]
    private string $proof;

    #[ManyToOne(
        targetEntity: Donee::class,
        inversedBy: 'infaqDistributions',
        cascade: ['persist']
    )]
    #[JoinColumn(name: 'doneeRefId', referencedColumnName: 'id')]
    private Donee $donee;

    #[OneToOne(
        targetEntity: Transaction::class,
        mappedBy: 'infaqDistribution',
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
    public function getProgram(): int
    {
        return $this->program;
    }

    /**
     * @param int $program
     * @return void
     */
    public function setProgram(int $program): void
    {
        $this->program = $program;
    }

    /**
     * @return int
     */
    public function getFundingResource(): int
    {
        return $this->fundingResource;
    }

    /**
     * @param int $fundingResource
     * @return void
     */
    public function setFundingResource(int $fundingResource): void
    {
        $this->fundingResource = $fundingResource;
    }

    /**
     * @return int
     */
    public function getReceivingCategory(): int
    {
        return $this->receivingCategory;
    }

    /**
     * @param int $receivingCategory
     * @return void
     */
    public function setReceivingCategory(int $receivingCategory): void
    {
        $this->receivingCategory = $receivingCategory;
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
    public function getNumberOfBeneficiaries(): int
    {
        return $this->numberOfBeneficiaries;
    }

    /**
     * @param int $numberOfBeneficiaries
     * @return void
     */
    public function setNumberOfBeneficiaries(int $numberOfBeneficiaries): void
    {
        $this->numberOfBeneficiaries = $numberOfBeneficiaries;
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
     * @return \Lazis\Api\Entity\Donee
     */
    public function getDonee(): Donee
    {
        return $this->donee;
    }

    /**
     * @param \Lazis\Api\Entity\Donee $donee
     * @return void
     */
    public function setDonee(Donee $donee): void
    {
        $this->donee = $donee;
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
        return '__infaqDistribution__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'infaqDistribution';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return InfaqDistribution::class;
    }
}
