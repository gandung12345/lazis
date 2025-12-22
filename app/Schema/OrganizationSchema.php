<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Rule;
use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Regex;
use Schnell\Attribute\Schema\Enum;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class OrganizationSchema extends AbstractSchema
{
    use SchemaTrait;

    #[Rule(required: true)]
    #[Json(name: 'name')]
    private ?string $name;

    #[Rule(required: true)]
    #[Json(name: 'address')]
    private ?string $address;

    #[Rule(required: true)]
    #[Regex(pattern: self::PHONE_NUMBER_PATTERN)]
    #[Json(name: 'phoneNumber')]
    private ?string $phoneNumber;

    #[Rule(required: true)]
    #[Regex(pattern: self::EMAIL_PATTERN)]
    #[Json(name: 'email')]
    private ?string $email;

    #[Rule(required: true)]
    #[Enum(value: self::SCOPE_LIST)]
    #[Json(name: 'scope')]
    private ?int $scope;

    #[Rule(required: true)]
    #[Json(name: 'district')]
    private ?string $district;

    #[Rule(required: true)]
    #[Json(name: 'village')]
    private ?string $village;

    /**
     * @param string|null $name
     * @param string|null $address
     * @param string|null $phoneNumber
     * @param string|null $email
     * @param int|null $scope
     * @param string|null $district
     * @param string|null $village
     */
    public function __construct(
        ?string $name = null,
        ?string $address = null,
        ?string $phoneNumber = null,
        ?string $email = null,
        ?int $scope = null,
        ?string $district = null,
        ?string $village = null
    ) {
        $this->setName($name);
        $this->setAddress($address);
        $this->setPhoneNumber($phoneNumber);
        $this->setEmail($email);
        $this->setScope($scope);
        $this->setDistrict($district);
        $this->setVillage($village);
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
}
