<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\ChainEnum;
use Schnell\Attribute\Schema\Enum;
use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Regex;
use Schnell\Attribute\Schema\Rule;
use Schnell\Attribute\Schema\TransformedClassType;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class OrganizerSchema extends AbstractSchema
{
    use SchemaTrait;

    #[Rule(required: true)]
    #[Json(name: 'name')]
    private ?string $name;

    #[Rule(required: true)]
    #[Json(name: 'email')]
    #[Regex(pattern: self::EMAIL_PATTERN)]
    private ?string $email;

    #[Rule(required: true)]
    #[Json(name: 'identityNumber')]
    private ?string $identityNumber;

    #[Rule(required: true)]
    #[Json(name: 'dateOfBirth')]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $dateOfBirth;

    #[Rule(required: true)]
    #[Json(name: 'placeOfBirth')]
    private ?string $placeOfBirth;

    #[Rule(required: true)]
    #[Json(name: 'phoneNumber')]
    #[Regex(pattern: self::PHONE_NUMBER_PATTERN)]
    private ?string $phoneNumber;

    #[Rule(required: true)]
    #[Json(name: 'sex')]
    #[Enum(value: self::SEX_LIST)]
    private ?int $sex;

    #[Rule(required: true)]
    #[Json(name: 'neighborhoodAssoc')]
    private ?string $neighborhoodAssoc;

    #[Rule(required: true)]
    #[Json(name: 'communityAssoc')]
    private ?string $communityAssoc;

    #[Rule(required: true)]
    #[Json(name: 'hamlet')]
    private ?string $hamlet;

    #[Rule(required: true)]
    #[Json(name: 'village')]
    private ?string $village;

    #[Rule(required: true)]
    #[Json(name: 'district')]
    private ?string $district;

    #[Rule(required: true)]
    #[Json(name: 'address')]
    private ?string $address;

    #[Rule(required: true)]
    #[Json(name: 'occupation')]
    private ?string $occupation;

    #[Rule(required: true)]
    #[Json(name: 'education')]
    #[Enum(value: self::EDUCATION_LIST)]
    private ?int $education;

    #[Enum(value: self::SCOPE_LIST)]
    #[Rule(required: true)]
    #[Json(name: 'scope')]
    private ?int $scope;

    #[Rule(required: true)]
    #[Json(name: 'role')]
    #[ChainEnum(field: 'scope', value: self::ROLE_LIST)]
    private ?int $role;

    #[Rule(required: true)]
    #[Json(name: 'photo')]
    private ?string $photo;

    #[Rule(required: true)]
    #[Json(name: 'identityPhoto')]
    private ?string $identityPhoto;

    #[Rule(required: true)]
    #[Json(name: 'qrCode')]
    private ?string $qrCode;

    #[Rule(required: true)]
    #[Json(name: 'legalityDoc')]
    private ?string $legalityDoc;

    /**
     * @param string|null $name
     * @param string|null $email
     * @param string|null $identityNumber
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $dateOfBirth
     * @param string|null $placeOfBirth
     * @param string|null $phoneNumber
     * @param int|null $sex
     * @param string|null $neighborhoodAssoc
     * @param string|null $communityAssoc
     * @param string|null $hamlet
     * @param string|null $village
     * @param string|null $district
     * @param string|null $address
     * @param string|null $occupation
     * @param int|null $education
     * @param int|null $role
     * @param int|null $scope
     * @param string|null $photo
     * @param string|null $identityPhoto
     * @param string|null $qrCode
     * @param string|null $legalityDoc
     * @return static
     */
    public function __construct(
        ?string $name = null,
        ?string $email = null,
        ?string $identityNumber = null,
        ?DateTimeDecorator $dateOfBirth = null,
        ?string $placeOfBirth = null,
        ?string $phoneNumber = null,
        ?int $sex = null,
        ?string $neighborhoodAssoc = null,
        ?string $communityAssoc = null,
        ?string $hamlet = null,
        ?string $village = null,
        ?string $district = null,
        ?string $address = null,
        ?string $occupation = null,
        ?int $education = null,
        ?int $role = null,
        ?int $scope = null,
        ?string $photo = null,
        ?string $identityPhoto = null,
        ?string $qrCode = null,
        ?string $legalityDoc = null
    ) {
        $this->setName($name);
        $this->setEmail($email);
        $this->setIdentityNumber($identityNumber);
        $this->setDateOfBirth($dateOfBirth);
        $this->setPlaceOfBirth($placeOfBirth);
        $this->setPhoneNumber($phoneNumber);
        $this->setSex($sex);
        $this->setNeighborhoodAssoc($neighborhoodAssoc);
        $this->setCommunityAssoc($communityAssoc);
        $this->setHamlet($hamlet);
        $this->setVillage($village);
        $this->setDistrict($district);
        $this->setAddress($address);
        $this->setOccupation($occupation);
        $this->setEducation($education);
        $this->setRole($role);
        $this->setScope($scope);
        $this->setPhoto($photo);
        $this->setIdentityPhoto($identityPhoto);
        $this->setQrCode($qrCode);
        $this->setLegalityDoc($legalityDoc);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return void
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getIdentityNumber(): ?string
    {
        return $this->identityNumber;
    }

    /**
     * @param string|null $identityNumber
     * @return void
     */
    public function setIdentityNumber(?string $identityNumber): void
    {
        $this->identityNumber = $identityNumber;
    }

    /**
     * @return \Schnell\Decorator\Stringified\DateTimeDecorator|null
     */
    public function getDateOfBirth(): ?DateTimeDecorator
    {
        return $this->dateOfBirth;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $dateOfBirth
     * @return void
     */
    public function setDateOfBirth(?DateTimeDecorator $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return string|null
     */
    public function getPlaceOfBirth(): ?string
    {
        return $this->placeOfBirth;
    }

    /**
     * @param string|null $placeOfBirth
     * @return void
     */
    public function setPlaceOfBirth(?string $placeOfBirth): void
    {
        $this->placeOfBirth = $placeOfBirth;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     * @return void
     */
    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return int|null
     */
    public function getSex(): ?int
    {
        return $this->sex;
    }

    /**
     * @param int|null $sex
     * @return void
     */
    public function setSex(?int $sex): void
    {
        $this->sex = $sex;
    }

    /**
     * @return string|null
     */
    public function getNeighborhoodAssoc(): ?string
    {
        return $this->neighborhoodAssoc;
    }

    /**
     * @param string|null $neighborhoodAssoc
     * @return void
     */
    public function setNeighborhoodAssoc(?string $neighborhoodAssoc): void
    {
        $this->neighborhoodAssoc = $neighborhoodAssoc;
    }

    /**
     * @return string|null
     */
    public function getCommunityAssoc(): ?string
    {
        return $this->communityAssoc;
    }

    /**
     * @param string|null $communityAssoc
     * @return void
     */
    public function setCommunityAssoc(?string $communityAssoc): void
    {
        $this->communityAssoc = $communityAssoc;
    }

    /**
     * @return string|null
     */
    public function getHamlet(): ?string
    {
        return $this->hamlet;
    }

    /**
     * @param string|null $hamlet
     * @return void
     */
    public function setHamlet(?string $hamlet): void
    {
        $this->hamlet = $hamlet;
    }

    /**
     * @return string|null
     */
    public function getVillage(): ?string
    {
        return $this->village;
    }

    /**
     * @param string|null $village
     * @return void
     */
    public function setVillage(?string $village): void
    {
        $this->village = $village;
    }

    /**
     * @return string|null
     */
    public function getDistrict(): ?string
    {
        return $this->district;
    }

    /**
     * @param string|null $district
     * @return void
     */
    public function setDistrict(?string $district): void
    {
        $this->district = $district;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     * @return void
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getOccupation(): ?string
    {
        return $this->occupation;
    }

    /**
     * @param string|null $occupation
     * @return void
     */
    public function setOccupation(?string $occupation): void
    {
        $this->occupation = $occupation;
    }

    /**
     * @return int|null
     */
    public function getEducation(): ?int
    {
        return $this->education;
    }

    /**
     * @param int|null $education
     * @return void
     */
    public function setEducation(?int $education): void
    {
        $this->education = $education;
    }

    /**
     * @return int|null
     */
    public function getRole(): ?int
    {
        return $this->role;
    }

    /**
     * @param int|null $role
     * @return void
     */
    public function setRole(?int $role): void
    {
        $this->role = $role;
    }

    /**
     * @return int|null
     */
    public function getScope(): ?int
    {
        return $this->scope;
    }

    /**
     * @param int|null $scope
     * @return void
     */
    public function setScope(?int $scope): void
    {
        $this->scope = $scope;
    }

    /**
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param string|null $photo
     * @return void
     */
    public function setPhoto(?string $photo): void
    {
        $this->photo = $photo;
    }

    /**
     * @return string|null
     */
    public function getIdentityPhoto(): ?string
    {
        return $this->identityPhoto;
    }

    /**
     * @param string|null $identityPhoto
     * @return void
     */
    public function setIdentityPhoto(?string $identityPhoto): void
    {
        $this->identityPhoto = $identityPhoto;
    }

    /**
     * @return string|null
     */
    public function getQrCode(): ?string
    {
        return $this->qrCode;
    }

    /**
     * @param string|null $qrCode
     * @return void
     */
    public function setQrCode(?string $qrCode): void
    {
        $this->qrCode = $qrCode;
    }

    /**
     * @return string|null
     */
    public function getLegalityDoc(): ?string
    {
        return $this->legalityDoc;
    }

    /**
     * @param string|null $legalityDoc
     * @return void
     */
    public function setLegalityDoc(?string $legalityDoc): void
    {
        $this->legalityDoc = $legalityDoc;
    }
}
