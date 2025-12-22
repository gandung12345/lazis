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
use Schnell\Attribute\Entity\GeneratedError;
use Schnell\Attribute\Schema\Json;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\AbstractEntity;
use Schnell\Mapper\Query\Error as QueryError;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'organizer')]
#[GeneratedError(
    targetColumn: 'email',
    sqlStatePrefix: QueryError::SQLSTATE_PREF27,
)]
#[OpenApi\Schema]
class Organizer extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', unique: true, nullable: false)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Organizer ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'name')]
    #[OpenApi\Property(
        property: 'name',
        type: 'string',
        description: 'Organizer name'
    )]
    private string $name;

    #[Column(type: 'text', nullable: false, unique: true)]
    #[Json(name: 'email')]
    #[OpenApi\Property(
        property: 'email',
        type: 'string',
        description: 'Organizer email'
    )]
    private string $email;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'identityNumber')]
    #[OpenApi\Property(
        property: 'identityNumber',
        type: 'string',
        description: 'Organizer identity number'
    )]
    private string $identityNumber;

    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'dateOfBirth')]
    #[OpenApi\Property(
        property: 'dateOfBirth',
        type: 'string',
        format: 'date',
        description: 'Organizer date of birth'
    )]
    private DateTimeDecorator $dateOfBirth;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'placeOfBirth')]
    #[OpenApi\Property(
        property: 'placeOfBirth',
        type: 'string',
        description: 'Organizer place of birth'
    )]
    private string $placeOfBirth;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'phoneNumber')]
    #[OpenApi\Property(
        property: 'phoneNumber',
        type: 'string',
        description: 'Organizer phone number'
    )]
    private string $phoneNumber;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'sex')]
    #[OpenApi\Property(
        property: 'sex',
        type: 'integer',
        description: 'Organizer sex type'
    )]
    private int $sex;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'neighborhoodAssoc')]
    #[OpenApi\Property(
        property: 'neighborhoodAssoc',
        type: 'string',
        description: 'Organizer neighborhood association'
    )]
    private string $neighborhoodAssoc;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'communityAssoc')]
    #[OpenApi\Property(
        property: 'communityAssoc',
        type: 'string',
        description: 'Organizer community association'
    )]
    private string $communityAssoc;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'hamlet')]
    #[OpenApi\Property(
        property: 'hamlet',
        type: 'string',
        description: 'Organizer hamlet'
    )]
    private string $hamlet;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'village')]
    #[OpenApi\Property(
        property: 'village',
        type: 'string',
        description: 'Organizer village'
    )]
    private string $village;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'district')]
    #[OpenApi\Property(
        property: 'district',
        type: 'string',
        description: 'Organizer district'
    )]
    private string $district;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'address')]
    #[OpenApi\Property(
        property: 'address',
        type: 'string',
        description: 'Organizer address'
    )]
    private string $address;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'occupation')]
    #[OpenApi\Property(
        property: 'occupation',
        type: 'string',
        description: 'Organizer occupation'
    )]
    private string $occupation;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'education')]
    #[OpenApi\Property(
        property: 'education',
        type: 'integer',
        description: 'Organizer education'
    )]
    private int $education;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'role')]
    #[OpenApi\Property(
        property: 'role',
        type: 'integer',
        description: 'Organizer role'
    )]
    // picklist: HierarchicalRole
    private int $role;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'scope')]
    #[OpenApi\Property(
        property: 'scope',
        type: 'integer',
        description: 'Organizer scope'
    )]
    // picklist: Scope
    private int $scope;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'photo')]
    #[OpenApi\Property(
        property: 'photo',
        type: 'string',
        description: 'Organizer photo'
    )]
    private string $photo;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'identityPhoto')]
    #[OpenApi\Property(
        property: 'identityPhoto',
        type: 'string',
        description: 'Organizer identity photo'
    )]
    private string $identityPhoto;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'qrCode')]
    #[OpenApi\Property(
        property: 'qrCode',
        type: 'string',
        description: 'Organizer QR code'
    )]
    private string $qrCode;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'legalityDoc')]
    #[OpenApi\Property(
        property: 'legalityDoc',
        type: 'string',
        description: 'Organizer legality document'
    )]
    private string $legalityDoc;

    #[OneToOne(
        targetEntity: Users::class,
        mappedBy: 'organizer',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Users $users;

    #[ManyToOne(
        targetEntity: Organization::class,
        inversedBy: 'organizers',
        cascade: ['persist']
    )]
    #[JoinColumn(name: 'organizationRefId', referencedColumnName: 'id')]
    private Organization $organization;

    #[OneToOne(
        targetEntity: Amil::class,
        mappedBy: 'organizer',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Amil $amil;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
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
     * @return \Schnell\Decorator\Stringified\DateTimeDecorator
     */
    public function getDateOfBirth(): DateTimeDecorator
    {
        return $this->dateOfBirth;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator $dateOfBirth
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
    public function getRole(): int
    {
        return $this->role;
    }

    /**
     * @param int $role
     * @return void
     */
    public function setRole(int $role): void
    {
        $this->role = $role;
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
     * @return string
     */
    public function getLegalityDoc(): string
    {
        return $this->legalityDoc;
    }

    /**
     * @param string $legalityDoc
     * @return void
     */
    public function setLegalityDoc(string $legalityDoc): void
    {
        $this->legalityDoc = $legalityDoc;
    }

    /**
     * @return Lazis\Api\Entity\Users
     */
    public function getUsers(): Users
    {
        return $this->users;
    }

    /**
     * @param Lazis\Api\Entity\Users $users
     * @return void
     */
    public function setUsers(Users $users): void
    {
        $this->users = $users;
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
     * @return Lazis\Api\Entity\Amil
     */
    public function getAmil(): Amil
    {
        return $this->amil;
    }

    /**
     * @param Lazis\Api\Entity\Amil $amil
     * @return void
     */
    public function setAmil(Amil $amil): void
    {
        $this->amil = $amil;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__organizer__';
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'organizer';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return Organizer::class;
    }
}
