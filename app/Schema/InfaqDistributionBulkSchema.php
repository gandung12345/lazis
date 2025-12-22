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
class InfaqDistributionBulkSchema extends AbstractSchema
{
    use SchemaTrait;

    #[Json(name: 'date')]
    #[Rule(required: true)]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $date;

    #[Json(name: 'program')]
    #[Rule(required: true)]
    #[Enum(value: self::ZAKAT_DISTRIBUTION_LIST)]
    private ?int $program;

    #[Json(name: 'fundingResource')]
    #[Rule(required: true)]
    #[Enum(value: self::WALLET_TYPE_LIST)]
    private ?int $fundingResource;

    #[Json(name: 'receivingCategory')]
    #[Rule(required: true)]
    #[Enum(value: self::MUZAKKI_LIST)]
    private ?int $receivingCategory;

    #[Json(name: 'name')]
    #[Rule(required: true)]
    private ?string $name;

    #[Json(name: 'address')]
    #[Rule(required: true)]
    private ?string $address;

    #[Json(name: 'phoneNumber')]
    #[Rule(required: true)]
    private ?string $phoneNumber;

    #[Json(name: 'numberOfBeneficiaries')]
    #[Rule(required: true)]
    private ?int $numberOfBeneficiaries;

    #[Json(name: 'amount')]
    #[Rule(required: true)]
    private ?int $amount;

    #[Json(name: 'description')]
    #[Rule(required: true)]
    private ?string $description;

    #[Json(name: 'proof')]
    #[Rule(required: true)]
    private ?string $proof;

    #[Json(name: 'doneeId')]
    #[Rule(required: true)]
    private ?string $doneeId;

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $date
     * @param int|null $program
     * @param int|null $fundingResource
     * @param int|null $receivingCategory
     * @param string|null $name
     * @param string|null $address
     * @param string|null $phoneNumber
     * @param int|null $numberOfBeneficiaries
     * @param int|null $amount
     * @param string|null $description
     * @param string|null $proof
     * @param string|null $doneeId
     */
    public function __construct(
        ?DateTimeDecorator $date = null,
        ?int $program = null,
        ?int $fundingResource = null,
        ?int $receivingCategory = null,
        ?string $name = null,
        ?string $address = null,
        ?string $phoneNumber = null,
        ?int $numberOfBeneficiaries = null,
        ?int $amount = null,
        ?string $description = null,
        ?string $proof = null,
        ?string $doneeId = null
    ) {
        $this->setDate($date);
        $this->setProgram($program);
        $this->setFundingResource($fundingResource);
        $this->setReceivingCategory($receivingCategory);
        $this->setName($name);
        $this->setAddress($address);
        $this->setPhoneNumber($phoneNumber);
        $this->setNumberOfBeneficiaries($numberOfBeneficiaries);
        $this->setAmount($amount);
        $this->setDescription($description);
        $this->setProof($proof);
        $this->setDoneeId($doneeId);
    }

    /**
     * @return \Schnell\Decorator\Stringified\DateTimeDecorator|null
     */
    public function getDate(): ?DateTimeDecorator
    {
        return $this->date;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $date
     * @return void
     */
    public function setDate(?DateTimeDecorator $date): void
    {
        $this->date = $date;
    }

    /**
     * @return int|null
     */
    public function getProgram(): ?int
    {
        return $this->program;
    }

    /**
     * @param int|null $program
     * @return void
     */
    public function setProgram(?int $program): void
    {
        $this->program = $program;
    }

    /**
     * @return int|null
     */
    public function getFundingResource(): ?int
    {
        return $this->fundingResource;
    }

    /**
     * @param int|null $fundingResource
     * @return void
     */
    public function setFundingResource(?int $fundingResource): void
    {
        $this->fundingResource = $fundingResource;
    }

    /**
     * @return int|null
     */
    public function getReceivingCategory(): ?int
    {
        return $this->receivingCategory;
    }

    /**
     * @param int|null $receivingCategory
     * @return void
     */
    public function setReceivingCategory(?int $receivingCategory): void
    {
        $this->receivingCategory = $receivingCategory;
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
     * @return int|null
     */
    public function getNumberOfBeneficiaries(): ?int
    {
        return $this->numberOfBeneficiaries;
    }

    /**
     * @param int|null $numberOfBeneficiaries
     * @return void
     */
    public function setNumberOfBeneficiaries(?int $numberOfBeneficiaries): void
    {
        $this->numberOfBeneficiaries = $numberOfBeneficiaries;
    }

    /**
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @param int|null $amount
     * @return void
     */
    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getProof(): ?string
    {
        return $this->proof;
    }

    /**
     * @param string|null $proof
     * @return void
     */
    public function setProof(?string $proof): void
    {
        $this->proof = $proof;
    }

    /**
     * @return string|null
     */
    public function getDoneeId(): ?string
    {
        return $this->doneeId;
    }

    /**
     * @param string|null $doneeId
     * @return void
     */
    public function setDoneeId(?string $doneeId): void
    {
        $this->doneeId = $doneeId;
    }
}
