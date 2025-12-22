<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'mosque')]
#[OpenApi\Schema]
class Mosque extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Mosque ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'name')]
    #[OpenApi\Property(
        property: 'name',
        type: 'string',
        description: 'Mosque name'
    )]
    private string $name;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'type')]
    #[OpenApi\Property(
        property: 'type',
        type: 'integer',
        description: 'Mosque type'
    )]
    private int $type;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'leader')]
    #[OpenApi\Property(
        property: 'leader',
        type: 'string',
        description: 'Mosque leader'
    )]
    private string $leader;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'phoneNumber')]
    #[OpenApi\Property(
        property: 'phoneNumber',
        type: 'string',
        description: 'Mosque phone number'
    )]
    private string $phoneNumber;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'neighborhoodAssoc')]
    #[OpenApi\Property(
        property: 'neighborhoodAssoc',
        type: 'string',
        description: 'Mosque neighborhood association'
    )]
    private string $neighborhoodAssoc;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'communityAssoc')]
    #[OpenApi\Property(
        property: 'communityAssoc',
        type: 'string',
        description: 'Mosque community association'
    )]
    private string $communityAssoc;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'hamlet')]
    #[OpenApi\Property(
        property: 'hamlet',
        type: 'string',
        description: 'Mosque hamlet'
    )]
    private string $hamlet;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'village')]
    #[OpenApi\Property(
        property: 'village',
        type: 'string',
        description: 'Mosque village'
    )]
    private string $village;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'district')]
    #[OpenApi\Property(
        property: 'district',
        type: 'string',
        description: 'Mosque district'
    )]
    private string $district;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'address')]
    #[OpenApi\Property(
        property: 'address',
        type: 'string',
        description: 'Mosque address'
    )]
    private string $address;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'certificateNumber')]
    #[OpenApi\Property(
        property: 'certificateNumber',
        type: 'string',
        description: 'Mosque JPZIS certificate number'
    )]
    private string $certNumber;

    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'certificateDate')]
    #[OpenApi\Property(
        property: 'certificateDate',
        type: 'string',
        description: 'Mosque JPZIS certificate date'
    )]
    private DateTimeDecorator $certDate;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'certificateImage')]
    #[OpenApi\Property(
        property: 'certificateImage',
        type: 'string',
        description: 'Mosque JPZIS certificate image'
    )]
    private string $certImage;

    #[ManyToOne(targetEntity: Organization::class, inversedBy: 'mosques')]
    #[JoinColumn(name: 'organizationRefId', referencedColumnName: 'id')]
    private Organization $organization;

    #[OneToMany(
        targetEntity: OffBalanceSheet::class,
        mappedBy: 'mosque',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $offBalanceSheets;

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
    public function getLeader(): string
    {
        return $this->leader;
    }

    /**
     * @param string $leader
     * @return void
     */
    public function setLeader(string $leader): void
    {
        $this->leader = $leader;
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
    public function getCertNumber(): string
    {
        return $this->certNumber;
    }

    /**
     * @param string $certNumber
     * @return void
     */
    public function setCertNumber(string $certNumber): void
    {
        $this->certNumber = $certNumber;
    }

    /**
     * @return \Schnell\Decorator\Stringified\DateTimeDecorator
     */
    public function getCertDate(): DateTimeDecorator
    {
        return $this->certDate;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator $certDate
     * @return void
     */
    public function setCertDate(DateTimeDecorator $certDate): void
    {
        $this->certDate = $certDate;
    }

    /**
     * @return string
     */
    public function getCertImage(): string
    {
        return $this->certImage;
    }

    /**
     * @param string $certImage
     * @return void
     */
    public function setCertImage(string $certImage): void
    {
        $this->certImage = $certImage;
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
    public function getOffBalanceSheets(): PersistentCollection
    {
        return $this->offBalanceSheets;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $offBalanceSheets
     * @return void
     */
    public function setOffBalanceSheet(PersistentCollection $offBalanceSheets): void
    {
        $this->offBalanceSheets = $offBalanceSheets;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__mosque__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'mosque';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return Mosque::class;
    }
}
