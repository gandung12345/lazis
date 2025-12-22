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
#[Entity, Table(name: 'amilFundingUsage')]
#[OpenApi\Schema]
class AmilFundingUsage extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Amil funding ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'date')]
    #[OpenApi\Property(
        property: 'date',
        type: 'string',
        format: 'date',
        description: 'Amil funding date'
    )]
    private DateTimeDecorator $date;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'amount')]
    #[OpenApi\Property(
        property: 'amount',
        type: 'integer',
        description: 'Amil funding amount'
    )]
    private int $amount;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'usageType')]
    #[OpenApi\Property(
        property: 'usageType',
        type: 'integer',
        description: 'Amil funding usage type'
    )]
    private int $usageType;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'description')]
    #[OpenApi\Property(
        property: 'description',
        type: 'string',
        description: 'Amil funding description'
    )]
    private string $description;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'proof')]
    #[OpenApi\Property(
        property: 'proof',
        type: 'string',
        description: 'Amil funding proof'
    )]
    private string $proof;

    #[OneToOne(
        targetEntity: Transaction::class,
        mappedBy: 'amilFundingUsage',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Transaction $transaction;

    #[ManyToOne(targetEntity: Organization::class, inversedBy: 'amilFundingUsages')]
    #[JoinColumn(name: 'organizationRefId', referencedColumnName: 'id')]
    private Organization $organization;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string $id
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
     * @return int
     */
    public function getUsageType(): int
    {
        return $this->usageType;
    }

    /**
     * @param int $usageType
     * @return void
     */
    public function setUsageType(int $usageType): void
    {
        $this->usageType = $usageType;
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
     * @return \Lazis\Api\Entity\Organization
     */
    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    /**
     * @param \Lazis\Api\Entity\Organization $organization
     * @return void
     */
    public function setOrganization(Organization $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__amilFundingUsage__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'amilFundingUsage';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return AmilFundingUsage::class;
    }
}
