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
#[Entity, Table(name: 'nonHalalFundingReceive')]
#[OpenApi\Schema]
class NonHalalFundingReceive extends AbstractEntity
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
        description: 'Non halal funding receive ID'
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
        description: 'Non halal funding receive date'
    )]
    private DateTimeDecorator $date;

    /**
     * @var int
     */
    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'kind')]
    #[OpenApi\Property(
        property: 'kind',
        type: 'integer',
        description: 'Non halal funding receiving kind'
    )]
    private int $kind;

    /**
     * @var int
     */
    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'amount')]
    #[OpenApi\Property(
        property: 'amount',
        type: 'integer',
        description: 'Non halal funding receiving amount'
    )]
    private int $amount;

    /**
     * @var string
     */
    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'description')]
    #[OpenApi\Property(
        property: 'description',
        type: 'string',
        description: 'Non halal funding receiving description'
    )]
    private string $description;

    /**
     * @var string
     */
    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'proof')]
    #[OpenApi\Property(
        property: 'proof',
        type: 'string',
        description: 'Non halal funding receiving proof'
    )]
    private string $proof;

    /**
     * @var \Lazis\Api\Schema\Organization
     */
    #[ManyToOne(
        targetEntity: Organization::class,
        inversedBy: 'nonHalalFundingReceives'
    )]
    #[JoinColumn(name: 'organizationRefId', referencedColumnName: 'id')]
    private Organization $organization;

    /**
     * @var \Lazis\Api\Schema\Transaction
     */
    #[OneToOne(
        targetEntity: Transaction::class,
        mappedBy: 'nonHalalFundingReceive',
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
    public function getKind(): int
    {
        return $this->kind;
    }

    /**
     * @param int $kind
     * @return void
     */
    public function setKind(int $kind): void
    {
        $this->kind = $kind;
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
        return '_nonHalalFundingReceive__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'nonHalalFundingReceive';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return NonHalalFundingReceive::class;
    }
}
