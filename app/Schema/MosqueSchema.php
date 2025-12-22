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
class MosqueSchema extends AbstractSchema
{
    use SchemaTrait;

    /**
     * @var string|null
     */
    #[Json(name: 'name')]
    #[Rule(required: true)]
    private ?string $name;

    /**
     * @var int|null
     */
    #[Json(name: 'type')]
    #[Rule(required: true)]
    #[Enum(value: self::JPZIS_TYPE_LIST)]
    private ?int $type;

    /**
     * @var string|null
     */
    #[Json(name: 'leader')]
    #[Rule(required: true)]
    private ?string $leader;

    /**
     * @var string|null
     */
    #[Json(name: 'phoneNumber')]
    #[Regex(pattern: self::PHONE_NUMBER_PATTERN)]
    #[Rule(required: true)]
    private ?string $phoneNumber;

    /**
     * @var string|null
     */
    #[Json(name: 'neighborhoodAssoc')]
    #[Rule(required: true)]
    private ?string $neighborhoodAssoc;

    /**
     * @var string|null
     */
    #[Json(name: 'communityAssoc')]
    #[Rule(required: true)]
    private ?string $communityAssoc;

    /**
     * @var string|null
     */
    #[Json(name: 'hamlet')]
    #[Rule(required: true)]
    private ?string $hamlet;

    /**
     * @var string|null
     */
    #[Json(name: 'village')]
    #[Rule(required: true)]
    private ?string $village;

    /**
     * @var string|null
     */
    #[Json(name: 'district')]
    #[Rule(required: true)]
    private ?string $district;

    /**
     * @var string|null
     */
    #[Json(name: 'address')]
    #[Rule(required: true)]
    private ?string $address;

    /**
     * @var string|null
     */
    #[Json(name: 'certificateNumber')]
    #[Rule(required: true)]
    private ?string $certNumber;

    /**
     * @var \Schnell\Decorator\Stringified\DateTimeDecorator|null
     */
    #[Json(name: 'certificateDate')]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[Rule(required: true)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $certDate;

    /**
     * @var string|null
     */
    #[Json(name: 'certificateImage')]
    #[Rule(required: true)]
    private ?string $certImage;

    /**
     * @param string|null $name
     * @param string|null $leader
     * @param string|null $phoneNumber
     * @param string|null $neighborhoodAssoc
     * @param string|null $communityAssoc
     * @param string|null $hamlet
     * @param string|null $village
     * @param string|null $district
     * @param string|null $address
     * @param string|null $certNumber
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $certDate
     * @param string|null $certImage
     * @return static
     */
    public function __construct(
        ?string $name = null,
        ?string $leader = null,
        ?string $phoneNumber = null,
        ?string $neighborhoodAssoc = null,
        ?string $communityAssoc = null,
        ?string $hamlet = null,
        ?string $village = null,
        ?string $district = null,
        ?string $address = null,
        ?string $certNumber = null,
        ?DateTimeDecorator $certDate = null,
        ?string $certImage = null
    ) {
        $this->setName($name);
        $this->setLeader($leader);
        $this->setPhoneNumber($phoneNumber);
        $this->setNeighborhoodAssoc($neighborhoodAssoc);
        $this->setCommunityAssoc($communityAssoc);
        $this->setHamlet($hamlet);
        $this->setVillage($village);
        $this->setDistrict($district);
        $this->setAddress($address);
        $this->setCertNumber($certNumber);
        $this->setCertDate($certDate);
        $this->setCertImage($certImage);
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
     * @return string|null
     */
    public function getLeader(): ?string
    {
        return $this->leader;
    }

    /**
     * @param string|null $leader
     * @return void
     */
    public function setLeader(?string $leader): void
    {
        $this->leader = $leader;
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
    public function getCertNumber(): ?string
    {
        return $this->certNumber;
    }

    /**
     * @param string|null $certNumber
     * @return void
     */
    public function setCertNumber(?string $certNumber): void
    {
        $this->certNumber = $certNumber;
    }

    /**
     * @return \Schnell\Decorator\Stringified\DateTimeDecorator|null
     */
    public function getCertDate(): ?DateTimeDecorator
    {
        return $this->certDate;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $certDate
     * @return void
     */
    public function setCertDate(?DateTimeDecorator $certDate): void
    {
        $this->certDate = $certDate;
    }

    /**
     * @return string|null
     */
    public function getCertImage(): ?string
    {
        return $this->certImage;
    }

    /**
     * @param string|null $certImage
     * @return void
     */
    public function setCertImage(?string $certImage): void
    {
        $this->certImage = $certImage;
    }
}
