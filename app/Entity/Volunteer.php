<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'volunteer')]
#[OpenApi\Schema]
class Volunteer extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Volunteer ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'name')]
    #[OpenApi\Property(
        property: 'name',
        type: 'string',
        description: 'Volunteer name'
    )]
    private string $name;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'identityNumber')]
    #[OpenApi\Property(
        property: 'identityNumber',
        type: 'string',
        description: 'Volunteer identity number'
    )]
    private string $identityNumber;

    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'dateOfBirth')]
    #[OpenApi\Property(
        property: 'dateOfBirth',
        type: 'string',
        format: 'date',
        description: 'Volunteer date of birth'
    )]
    private DateTimeDecorator $dateOfBirth;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'placeOfBirth')]
    #[OpenApi\Property(
        property: 'placeOfBirth',
        type: 'string',
        description: 'Volunteer place of birth'
    )]
    private string $placeOfBirth;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'sex')]
    #[OpenApi\Property(
        property: 'sex',
        type: 'integer',
        description: 'Volunteer sex type'
    )]
    private int $sex;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'phoneNumber')]
    #[OpenApi\Property(
        property: 'phoneNumber',
        type: 'string',
        description: 'Volunteer phone number'
    )]
    private string $phoneNumber;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'neighborhoodAssoc')]
    #[OpenApi\Property(
        property: 'neighborhoodAssoc',
        type: 'string',
        description: 'Volunteer neighborhood association'
    )]
    private string $neighborhoodAssoc;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'communityAssoc')]
    #[OpenApi\Property(
        property: 'communityAssoc',
        type: 'string',
        description: 'Volunteer community association'
    )]
    private string $communityAssoc;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'hamlet')]
    #[OpenApi\Property(
        property: 'hamlet',
        type: 'string',
        description: 'Volunteer hamlet'
    )]
    private string $hamlet;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'village')]
    #[OpenApi\Property(
        property: 'village',
        type: 'string',
        description: 'Volunteer village'
    )]
    private string $village;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'district')]
    #[OpenApi\Property(
        property: 'district',
        type: 'string',
        description: 'Volunteer district'
    )]
    private string $district;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'address')]
    #[OpenApi\Property(
        property: 'address',
        type: 'string',
        description: 'Volunteer address'
    )]
    private string $address;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'occupation')]
    #[OpenApi\Property(
        property: 'occupation',
        type: 'string',
        description: 'Volunteer occupation'
    )]
    private string $occupation;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'education')]
    #[OpenApi\Property(
        property: 'education',
        type: 'integer',
        description: 'Volunteer education'
    )]
    private int $education;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'status')]
    #[OpenApi\Property(
        property: 'status',
        type: 'integer',
        description: 'Volunteer status'
    )]
    // picklist: ActiveStatus
    private int $status;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'photo')]
    #[OpenApi\Property(
        property: 'photo',
        type: 'string',
        description: 'Volunteer photo'
    )]
    private string $photo;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'identityPhoto')]
    #[OpenApi\Property(
        property: 'identityPhoto',
        type: 'string',
        description: 'Volunteer identity photo'
    )]
    private string $identityPhoto;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'qrCode')]
    #[OpenApi\Property(
        property: 'qrCode',
        type: 'string',
        description: 'Volunteer QR code'
    )]
    private string $qrCode;

    // picklist: (TODO) Volunteer
    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'type')]
    #[OpenApi\Property(
        property: 'type',
        type: 'integer',
        description: 'Volunteer type'
    )]
    private int $type;

    // picklist: VolunteerGroup
    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'volunteerGroup')]
    #[OpenApi\Property(
        property: 'volunteerGroup',
        type: 'integer',
        description: 'Volunteer group'
    )]
    private int $volunteerGroup;

    #[ManyToOne(targetEntity: Organization::class, inversedBy: 'volunteers')]
    #[JoinColumn(name: 'organizationRefId', referencedColumnName: 'id')]
    private Organization $organization;

    #[OneToMany(
        targetEntity: Donor::class,
        mappedBy: 'volunteer',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $donors;

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
    public function getEducation(): int
    {
        return $this->education;
    }

    /**
     * @param int $education
     * @return void
     */
    public function setEducation(int $education): void
    {
        $this->education = $education;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return void
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getPhoto(): string
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     * @return void
     */
    public function setPhoto(string $photo): void
    {
        $this->photo = $photo;
    }

    /**
     * @return string
     */
    public function getIdentityPhoto(): string
    {
        return $this->identityPhoto;
    }

    /**
     * @param string $identityPhoto
     * @return void
     */
    public function setIdentityPhoto(string $identityPhoto): void
    {
        $this->identityPhoto = $identityPhoto;
    }

    /**
     * @return string
     */
    public function getQrCode(): string
    {
        return $this->qrCode;
    }

    /**
     * @param string $qrCode
     * @return void
     */
    public function setQrCode(string $qrCode): void
    {
        $this->qrCode = $qrCode;
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
    public function getVolunteerGroup(): int
    {
        return $this->volunteerGroup;
    }

    /**
     * @param int $volunteerGroup
     * @return void
     */
    public function setVolunteerGroup(int $volunteerGroup): void
    {
        $this->volunteerGroup = $volunteerGroup;
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
     * @return Doctrine\ORM\PersistentCollection
     */
    public function getDonors(): PersistentCollection
    {
        return $this->donors;
    }

    /**
     * @param Doctrine\ORM\PersistentCollection
     * @return void
     */
    public function setDonors(PersistentCollection $donors): void
    {
        $this->donors = $donors;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__volunteer__';
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'volunteer';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return Volunteer::class;
    }
}
