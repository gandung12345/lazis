<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'wallet')]
#[OpenApi\Schema]
class Wallet extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Wallet ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'type')]
    #[OpenApi\Property(
        property: 'type',
        type: 'integer',
        description: 'Wallet type'
    )]
    // picklist: Wallet
    private int $type;

    #[Column(type: 'integer', nullable: false, options: ['default' => 0])]
    #[Json(name: 'amount')]
    #[OpenApi\Property(
        property: 'amount',
        type: 'integer',
        description: 'Wallet amount'
    )]
    private int $amount;

    #[ManyToOne(targetEntity: Organization::class, inversedBy: 'wallets')]
    #[JoinColumn(name: 'organizationRefId', referencedColumnName: 'id')]
    private Organization $organization;

    #[OneToMany(
        targetEntity: Transaction::class,
        mappedBy: 'wallet',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $transactions;

    /**
     * @return static
     */
    public function __construct()
    {
        $this->setAmount(0);
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
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getTransactions(): PersistentCollection
    {
        return $this->transactions;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $collection
     * @return void
     */
    public function setTransactions(PersistentCollection $collection): void
    {
        $this->transactions = $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__wallet__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'wallet';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return Wallet::class;
    }
}
