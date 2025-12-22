<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'bankAccount')]
#[OpenApi\Schema]
class BankAccount extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Bank account ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'accountNumber')]
    #[OpenApi\Property(
        property: 'accountNumber',
        type: 'string',
        description: 'Bank account number'
    )]
    private string $accountNumber;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'bankName')]
    #[OpenApi\Property(
        property: 'bankName',
        type: 'string',
        description: 'Bank name'
    )]
    private string $bankName;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'accountHolderName')]
    #[OpenApi\Property(
        property: 'accountHolderName',
        type: 'string',
        description: 'Account holder name'
    )]
    private string $accountHolderName;

    #[ManyToOne(targetEntity: Organization::class, inversedBy: 'bankAccounts')]
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
     * @param string $id
     * @return void
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     * @return void
     */
    public function setAccountNumber(string $accountNumber): void
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return string
     */
    public function getBankName(): string
    {
        return $this->bankName;
    }

    /**
     * @param string $bankName
     * @return void
     */
    public function setBankName(string $bankName): void
    {
        $this->bankName = $bankName;
    }

    /**
     * @return string
     */
    public function getAccountHolderName(): string
    {
        return $this->accountHolderName;
    }

    /**
     * @param string $accountHolderName
     * @return void
     */
    public function setAccountHolderName(string $accountHolderName): void
    {
        $this->accountHolderName = $accountHolderName;
    }

    /**
     * @return Lazis\Api\Entity\Organization
     */
    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    /**
     * @param Lazis\Api\Entity\Organization $organization
     * @return void
     */
    public function setOrganization(Organization $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__bankAccount__';
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'bankAccount';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return BankAccount::class;
    }
}
