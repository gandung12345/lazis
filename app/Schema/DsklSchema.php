<?php

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
class DsklSchema extends AbstractSchema
{
    use SchemaTrait;

    /**
     * @var \Schnell\Decorator\Stringified\DateTimeDecorator|null
     */
    #[Rule(required: true)]
    #[Json(name: 'date')]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $date;

    /**
     * @var int|null
     */
    #[Rule(required: true)]
    #[Json(name: 'category')]
    #[Enum(value: self::DSKL_LIST)]
    private ?int $category;

    /**
     * @var int|null
     */
    #[Rule(required: true)]
    #[Json(name: 'kind')]
    #[Enum(value: self::MUZAKKI_LIST)]
    private ?int $kind;

    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'name')]
    private ?string $name;

    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'address')]
    private ?string $address;

    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'phoneNumber')]
    #[Regex(pattern: self::PHONE_NUMBER_PATTERN)]
    private ?string $phoneNumber;

    /**
     * @var int|null
     */
    #[Rule(required: true)]
    #[Json(name: 'amount')]
    private ?int $amount;

    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'description')]
    private ?string $description;

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $date
     * @param int|null $category
     * @param int|null $kind
     * @param string|null $name
     * @param string|null $address
     * @param string|null $phoneNumber
     * @param int|null $amount
     * @param string|null $description
     * @return static
     */
    public function __construct(
        ?DateTimeDecorator $date = null,
        ?int $category = null,
        ?int $kind = null,
        ?string $name = null,
        ?string $address = null,
        ?string $phoneNumber = null,
        ?int $amount = null,
        ?string $description = null
    ) {
        $this->setDate($date);
        $this->setCategory($category);
        $this->setKind($kind);
        $this->setName($name);
        $this->setAddress($address);
        $this->setPhoneNumber($phoneNumber);
        $this->setAmount($amount);
        $this->setDescription($description);
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
    public function getCategory(): ?int
    {
        return $this->category;
    }

    /**
     * @param int|null $category
     * @return void
     */
    public function setCategory(?int $category): void
    {
        $this->category = $category;
    }

    /**
     * @return int|null
     */
    public function getKind(): ?int
    {
        return $this->kind;
    }

    /**
     * @param int|null $kind
     * @return void
     */
    public function setKind(?int $kind): void
    {
        $this->kind = $kind;
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
}
