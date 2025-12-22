<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\PersistentCollection;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'organization')]
#[OpenApi\Schema]
class Organization extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Organization ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'name')]
    #[OpenApi\Property(
        property: 'name',
        type: 'string',
        description: 'Organization name'
    )]
    private string $name;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'address')]
    #[OpenApi\Property(
        property: 'address',
        type: 'string',
        description: 'Organization address'
    )]
    private string $address;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'phoneNumber')]
    #[OpenApi\Property(
        property: 'phoneNumber',
        type: 'string',
        description: 'Organization phone number'
    )]
    private string $phoneNumber;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'email')]
    #[OpenApi\Property(
        property: 'email',
        type: 'string',
        description: 'Organization email'
    )]
    private string $email;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'scope')]
    #[OpenApi\Property(
        property: 'scope',
        type: 'integer',
        description: 'Organization scope'
    )]
    private int $scope;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'district')]
    #[OpenApi\Property(
        property: 'district',
        type: 'string',
        description: 'Organization district'
    )]
    private string $district;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'village')]
    #[OpenApi\Property(
        property: 'village',
        type: 'string',
        description: 'Organization village'
    )]
    private string $village;

    #[OneToOne(
        targetEntity: Legal::class,
        mappedBy: 'organization',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Legal $legal;

    #[OneToMany(
        targetEntity: Organizer::class,
        mappedBy: 'organization',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $organizers;

    #[OneToMany(
        targetEntity: BankAccount::class,
        mappedBy: 'organization',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $bankAccounts;

    #[OneToMany(
        targetEntity: Volunteer::class,
        mappedBy: 'organization',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $volunteers;

    #[OneToMany(
        targetEntity: Donee::class,
        mappedBy: 'organization',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $donees;

    #[OneToMany(
        targetEntity: Wallet::class,
        mappedBy: 'organization',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $wallets;

    #[OneToMany(
        targetEntity: WalletMutation::class,
        mappedBy: 'organization',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $walletMutations;

    #[OneToMany(
        targetEntity: Mosque::class,
        mappedBy: 'organization',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $mosques;

    #[OneToMany(
        targetEntity: NonHalalFundingReceive::class,
        mappedBy: 'organization',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $nonHalalFundingReceives;

    #[OneToMany(
        targetEntity: NonHalalFundingDistribution::class,
        mappedBy: 'organization',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $nonHalalFundingDistributions;

    #[OneToMany(
        targetEntity: AmilFundingUsage::class,
        mappedBy: 'organization',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $amilFundingUsages;

    #[OneToMany(
        targetEntity: AssetRecording::class,
        mappedBy: 'organization',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $assetRecordings;

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
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getScope(): int
    {
        return $this->scope;
    }

    /**
     * @param int $scope
     * @return void
     */
    public function setScope(int $scope): void
    {
        $this->scope = $scope;
    }

    /**
     * @return string
     */
    public function getDistrict(): string
    {
        return $this->district;
    }

    /**
     * @param string $district
     * @return void
     */
    public function setDistrict(string $district): void
    {
        $this->district = $district;
    }

    /**
     * @return string
     */
    public function getVillage(): string
    {
        return $this->village;
    }

    /**
     * @param string $village
     * @return void
     */
    public function setVillage(string $village): void
    {
        $this->village = $village;
    }

    /**
     * @return Schnell\Api\Entity\Legal
     */
    public function getLegal(): Legal
    {
        return $this->legal;
    }

    /**
     * @param \Lazis\Api\Entity\Legal $legal
     * @return void
     */
    public function setLegal(Legal $legal): void
    {
        $this->legal = $legal;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getBankAccounts(): PersistentCollection
    {
        return $this->bankAccounts;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $collection
     * @return void
     */
    public function setBankAccounts(PersistentCollection $collection): void
    {
        $this->bankAccounts = $collection;
    }

    /**
     * @return Doctrine\ORM\PersistentCollection
     */
    public function getOrganizers(): PersistentCollection
    {
        return $this->organizers;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $collection
     * @return void
     */
    public function setOrganizers(PersistentCollection $collection): void
    {
        $this->organizers = $collection;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getVolunteers(): PersistentCollection
    {
        return $this->volunteers;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $collection
     * @return void
     */
    public function setVolunteers(PersistentCollection $collection): void
    {
        $this->volunteers = $collection;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getDonees(): PersistentCollection
    {
        return $this->donees;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $collection
     * @return void
     */
    public function setDonees(PersistentCollection $collection): void
    {
        $this->donees = $collection;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getWallets(): PersistentCollection
    {
        return $this->wallets;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $collection
     * @return void
     */
    public function setWallets(PersistentCollection $collection): void
    {
        $this->wallets = $collection;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getWalletMutations(): PersistentCollection
    {
        return $this->walletMutations;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $walletMutations
     * @return void
     */
    public function setWalletMutations(PersistentCollection $walletMutations): void
    {
        $this->walletMutations = $walletMutations;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getMosques(): PersistentCollection
    {
        return $this->mosques;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $collection
     * @return void
     */
    public function setMosques(PersistentCollection $collection): void
    {
        $this->mosques = $collection;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getNonHalalFundingReceives(): PersistentCollection
    {
        return $this->nonHalalFundingReceives;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $nonHalalFundingReceives
     * @return void
     */
    public function setNonHalalFundingReceives(
        PersistentCollection $nonHalalFundingReceives
    ): void {
        $this->nonHalalFundingReceives = $nonHalalFundingReceives;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getNonHalalFundingDistributions(): PersistentCollection
    {
        return $this->nonHalalFundingDistributions;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $nonHalalFundingDistributions
     * @return void
     */
    public function setNonHalalFundingDistributions(
        PersistentCollection $nonHalalFundingDistributions
    ): void {
        $this->nonHalalFundingDistributions = $nonHalalFundingDistributions;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getAmilFundingUsages(): PersistentCollection
    {
        return $this->amilFundingUsages;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $amilFundingUsages
     * @return void
     */
    public function setAmilFundingUsages(PersistentCollection $amilFundingUsages): void
    {
        $this->amilFundingUsages = $amilFundingUsages;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getAssetRecordings(): PersistentCollection
    {
        return $this->assetRecordings;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $assetRecordings
     * @return void
     */
    public function setAssetRecordings(PersistentCollection $assetRecordings): void
    {
        $this->assetRecordings = $assetRecordings;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__organization__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'organization';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return Organization::class;
    }
}
