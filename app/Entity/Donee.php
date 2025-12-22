<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\PersistentCollection;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'donee')]
#[OpenApi\Schema]
class Donee extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Donee ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'identityNumber')]
    #[OpenApi\Property(
        property: 'identityNumber',
        type: 'string',
        description: 'Donee identity number'
    )]
    private string $identityNumber;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'name')]
    #[OpenApi\Property(
        property: 'name',
        type: 'string',
        description: 'Donee name'
    )]
    private string $name;

    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'dateOfBirth')]
    #[OpenApi\Property(
        property: 'dateOfBirth',
        type: 'string',
        format: 'date',
        description: 'Donee date of birth'
    )]
    private DateTimeDecorator $dateOfBirth;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'placeOfBirth')]
    #[OpenApi\Property(
        property: 'placeOfBirth',
        type: 'string',
        description: 'placeOfBirth'
    )]
    private string $placeOfBirth;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'sex')]
    #[OpenApi\Property(
        property: 'sex',
        type: 'integer',
        description: 'Donee sex type'
    )]
    private int $sex;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'phoneNumber')]
    #[OpenApi\Property(
        property: 'phoneNumber',
        type: 'string',
        description: 'Donee phone number'
    )]
    private string $phoneNumber;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'neighborhoodAssoc')]
    #[OpenApi\Property(
        property: 'neighborhoodAssoc',
        type: 'string',
        description: 'Donee neighborhood association'
    )]
    private string $neighborhoodAssoc;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'communityAssoc')]
    #[OpenApi\Property(
        property: 'communityAssoc',
        type: 'string',
        description: 'Donee community association'
    )]
    private string $communityAssoc;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'hamlet')]
    #[OpenApi\Property(
        property: 'hamlet',
        type: 'string',
        description: 'Donee hamlet'
    )]
    private string $hamlet;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'village')]
    #[OpenApi\Property(
        property: 'village',
        type: 'string',
        description: 'Donee village'
    )]
    private string $village;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'district')]
    #[OpenApi\Property(
        property: 'district',
        type: 'string',
        description: 'Donee district'
    )]
    private string $district;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'address')]
    #[OpenApi\Property(
        property: 'address',
        type: 'string',
        description: 'Donee address'
    )]
    private string $address;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'occupation')]
    #[OpenApi\Property(
        property: 'occupation',
        type: 'string',
        description: 'Donee occupation'
    )]
    private string $occupation;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'asnaf')]
    #[OpenApi\Property(
        property: 'asnaf',
        type: 'integer',
        description: 'Donee asnaf type'
    )]
    // picklist: Asnaf
    private int $asnaf;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'category')]
    #[OpenApi\Property(
        property: 'category',
        type: 'integer',
        description: 'Donee category'
    )]
    // picklist: Donee
    private int $category;

    #[ManyToOne(targetEntity: Organization::class, inversedBy: 'donees')]
    #[JoinColumn(name: 'organizationRefId', referencedColumnName: 'id')]
    private Organization $organization;

    #[OneToMany(
        targetEntity: ZakatDistribution::class,
        mappedBy: 'donee',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $zakatDistributions;

    #[OneToMany(
        targetEntity: InfaqDistribution::class,
        mappedBy: 'donee',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $infaqDistributions;

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
    public function getIdentityNumber(): string
    {
        return $this->identityNumber;
    }

    /**
     * @param string $identityNumber
     * @return void
     */
    public function setIdentityNumber(string $identityNumber): void
    {
        $this->identityNumber = $identityNumber;
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
     * @return Schnell\Decorator\Stringified\DateTimeDecorator
     */
    public function getDateOfBirth(): DateTimeDecorator
    {
        return $this->dateOfBirth;
    }

    /**
     * @param Schnell\Decorator\Stringified\DateTimeDecorator $dateOfBirth
     * @return void
     */
    public function setDateOfBirth(DateTimeDecorator $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return string
     */
    public function getPlaceOfBirth(): string
    {
        return $this->placeOfBirth;
    }

    /**
     * @param string $placeOfBirth
     * @return void
     */
    public function setPlaceOfBirth(string $placeOfBirth): void
    {
        $this->placeOfBirth = $placeOfBirth;
    }

    /**
     * @return int
     */
    public function getSex(): int
    {
        return $this->sex;
    }

    /**
     * @param int $sex
     * @return void
     */
    public function setSex(int $sex): void
    {
        $this->sex = $sex;
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
    public function getNeighborhoodAssoc(): string
    {
        return $this->neighborhoodAssoc;
    }

    /**
     * @param string $neighborhoodAssoc
     * @return void
     */
    public function setNeighborhoodAssoc(string $neighborhoodAssoc): void
    {
        $this->neighborhoodAssoc = $neighborhoodAssoc;
    }

    /**
     * @return string
     */
    public function getCommunityAssoc(): string
    {
        return $this->communityAssoc;
    }

    /**
     * @param string $communityAssoc
     * @return void
     */
    public function setCommunityAssoc(string $communityAssoc): void
    {
        $this->communityAssoc = $communityAssoc;
    }

    /**
     * @return string
     */
    public function getHamlet(): string
    {
        return $this->hamlet;
    }

    /**
     * @param string $hamlet
     * @return void
     */
    public function setHamlet(string $hamlet): void
    {
        $this->hamlet = $hamlet;
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
    public function getOccupation(): string
    {
        return $this->occupation;
    }

    /**
     * @param string $occupation
     * @return void
     */
    public function setOccupation(string $occupation): void
    {
        $this->occupation = $occupation;
    }

    /**
     * @return int
     */
    public function getAsnaf(): int
    {
        return $this->asnaf;
    }

    /**
     * @param int $asnaf
     * @return void
     */
    public function setAsnaf(int $asnaf): void
    {
        $this->asnaf = $asnaf;
    }

    /**
     * @return int
     */
    public function getCategory(): int
    {
        return $this->category;
    }

    /**
     * @param int $category
     * @return void
     */
    public function setCategory(int $category): void
    {
        $this->category = $category;
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
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getZakatDistributions(): PersistentCollection
    {
        return $this->zakatDistributions;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection
     * @return void
     */
    public function setZakatDistributions(PersistentCollection $zakatDistributions): void
    {
        $this->zakatDistributions = $zakatDistributions;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getInfaqDistributions(): PersistentCollection
    {
        return $this->infaqDistributions;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollections $infaqDistributions
     * @return void
     */
    public function setInfaqDistributions(PersistentCollection $infaqDistributions): void
    {
        $this->infaqDistributions = $infaqDistributions;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__donee__';
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'donee';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return Donee::class;
    }
}
