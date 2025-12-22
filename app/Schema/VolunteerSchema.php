<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

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
class VolunteerSchema extends AbstractSchema
{
    use SchemaTrait;

    #[Json(name: 'name')]
    #[Rule(required: true)]
    private ?string $name;

    #[Json(name: 'identityNumber')]
    #[Rule(required: true)]
    private ?string $identityNumber;

    #[Json(name: 'dateOfBirth')]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[Rule(required: true)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $dateOfBirth;

    #[Json(name: 'placeOfBirth')]
    #[Rule(required: true)]
    private ?string $placeOfBirth;

    #[Enum(value: self::SEX_LIST)]
    #[Json(name: 'sex')]
    #[Rule(required: true)]
    private ?int $sex;

    #[Json(name: 'phoneNumber')]
    #[Regex(pattern: self::PHONE_NUMBER_PATTERN)]
    #[Rule(required: true)]
    private ?string $phoneNumber;

    #[Json(name: 'neighborhoodAssoc')]
    #[Rule(required: true)]
    private ?string $neighborhoodAssoc;

    #[Json(name: 'communityAssoc')]
    #[Rule(required: true)]
    private ?string $communityAssoc;

    #[Json(name: 'hamlet')]
    #[Rule(required: true)]
    private ?string $hamlet;

    #[Json(name: 'village')]
    #[Rule(required: true)]
    private ?string $village;

    #[Json(name: 'district')]
    #[Rule(required: true)]
    private ?string $district;

    #[Json(name: 'address')]
    #[Rule(required: true)]
    private ?string $address;

    #[Json(name: 'occupation')]
    #[Rule(required: true)]
    private ?string $occupation;

    #[Json(name: 'education')]
    #[Rule(required: true)]
    #[Enum(value: self::EDUCATION_LIST)]
    private ?int $education;

    #[Json(name: 'status')]
    #[Rule(required: true)]
    #[Enum(value: self::ACTIVE_STATUS_LIST)]
    private ?int $status;

    #[Json(name: 'photo')]
    #[Rule(required: true)]
    private ?string $photo;

    #[Json(name: 'identityPhoto')]
    #[Rule(required: true)]
    private ?string $identityPhoto;

    #[Json(name: 'qrCode')]
    #[Rule(required: true)]
    private ?string $qrCode;

    #[Json(name: 'type')]
    #[Rule(required: true)]
    #[Enum(value: self::VOLUNTEER_LIST)]
    private ?int $type;

    #[Json(name: 'volunteerGroup')]
    #[Rule(required: true)]
    #[Enum(value: self::VOLUNTEER_GROUP_LIST)]
    private ?int $volunteerGroup;

    /**
     * @param string|null $name
     * @param string|null $identityNumber
     * @param Schnell\Decorator\Stringified\DateTimeDecorator|null $dateOfBirth
     * @param string|null $placeOfBirth
     * @param int|null $sex
     * @param string|null $phoneNumber
     * @param string|null $neighborhoodAssoc
     * @param string|null $communityAssoc
     * @param string|null $hamlet
     * @param string|null $village
     * @param string|null $district
     * @param string|null $address
     * @param string|null $occupation
     * @param int|null $education
     * @param int|null $status
     * @param string|null $photo
     * @param string|null $identityPhoto
     * @param string|null $qrCode
     * @param int|null $type
     * @param int|null $volunteerGroup
     * @return static
     */
    public function __construct(
        ?string $name = null,
        ?string $identityNumber = null,
        ?DateTimeDecorator $dateOfBirth = null,
        ?string $placeOfBirth = null,
        ?int $sex = null,
        ?string $phoneNumber = null,
        ?string $neighborhoodAssoc = null,
        ?string $communityAssoc = null,
        ?string $hamlet = null,
        ?string $village = null,
        ?string $district = null,
        ?string $address = null,
        ?string $occupation = null,
        ?int $education = null,
        ?int $status = null,
        ?string $photo = null,
        ?string $identityPhoto = null,
        ?string $qrCode = null,
        ?int $type = null,
        ?int $volunteerGroup = null
    ) {
        $this->setName($name);
        $this->setIdentityNumber($identityNumber);
        $this->setDateOfBirth($dateOfBirth);
        $this->setPlaceOfBirth($placeOfBirth);
        $this->setSex($sex);
        $this->setPhoneNumber($phoneNumber);
        $this->setNeighborhoodAssoc($neighborhoodAssoc);
        $this->setCommunityAssoc($communityAssoc);
        $this->setHamlet($hamlet);
        $this->setVillage($village);
        $this->setDistrict($district);
        $this->setAddress($address);
        $this->setOccupation($occupation);
        $this->setEducation($education);
        $this->setStatus($status);
        $this->setPhoto($photo);
        $this->setIdentityPhoto($identityPhoto);
        $this->setQrCode($qrCode);
        $this->setType($type);
        $this->setVolunteerGroup($volunteerGroup);
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
     * @return Schnell\Decorator\Stringified\DateTimeDecorator|null
     */
    public function getDateOfBirth(): ?DateTimeDecorator
    {
        return $this->dateOfBirth;
    }

    /**
     * @param Schnell\Decorator\Stringified\DateTimeDecorator|null $dateOfBirth
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
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     * @return void
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
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
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     * @return void
     */
    public function setType(?int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int|null
     */
    public function getVolunteerGroup(): ?int
    {
        return $this->volunteerGroup;
    }

    /**
     * @param int|null $volunteerGroup
     * @return void
     */
    public function setVolunteerGroup(?int $volunteerGroup): void
    {
        $this->volunteerGroup = $volunteerGroup;
    }
}
