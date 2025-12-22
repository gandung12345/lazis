<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'walletMutation')]
#[OpenApi\Schema]
class WalletMutation extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Wallet mutation ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'type')]
    #[OpenApi\Property(
        property: 'type',
        type: 'integer',
        description: 'Wallet mutation type'
    )]
    private int $type;

    #[Column(type: 'integer', nullable: false, options: ['default' => 0])]
    #[Json(name: 'amount')]
    #[OpenApi\Property(
        property: 'amount',
        type: 'integer',
        description: 'Wallet mutation amount'
    )]
    private int $amount;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'year')]
    #[OpenApi\Property(
        property: 'year',
        type: 'integer',
        description: 'Wallet mutation year'
    )]
    private int $year;

    #[ManyToOne(targetEntity: Organization::class, inversedBy: 'walletMutations')]
    #[JoinColumns(name: 'organizationRefId', referencedColumnName: 'id')]
    private Organization $organization;

    /**
     * @return void
     */
    public function __construct()
    {
        $date = DateTimeDecorator::create()
            ->withFormat('Y');

        $this->setAmount(0);
        $this->setYear(intval($date->stringify()));
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
     * @param int
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
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     * @return void
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
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
        return '__walletMutation__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'walletMutation';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return WalletMutation::class;
    }
}
