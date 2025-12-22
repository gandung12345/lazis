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
class DonorSchema extends AbstractSchema
{
    use SchemaTrait;

    #[Rule(required: true)]
    #[Json(name: 'identityNumber')]
    private ?string $identityNumber;

    #[Rule(required: true)]
    #[Json(name: 'name')]
    private ?string $name;

    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[Rule(required: true)]
    #[Json(name: 'dateOfBirth')]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $dateOfBirth;

    #[Rule(required: true)]
    #[Json(name: 'placeOfBirth')]
    private ?string $placeOfBirth;

    #[Enum(value: self::SEX_LIST)]
    #[Rule(required: true)]
    #[Json(name: 'sex')]
    private ?int $sex;

    #[Regex(pattern: self::PHONE_NUMBER_PATTERN)]
    #[Rule(required: true)]
    #[Json(name: 'phoneNumber')]
    private ?string $phoneNumber;

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

    /**
     * @param string|null $identityNumber
     * @param string|null $name
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
     * @return static
     */
    public function __construct(
        ?string $identityNumber = null,
        ?string $name = null,
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
        ?string $occupation = null
    ) {
        $this->setIdentityNumber($identityNumber);
        $this->setName($name);
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
}
