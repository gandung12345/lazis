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
class ZakatSchema extends AbstractSchema
{
    use SchemaTrait;

    #[Rule(required: true)]
    #[Json(name: 'date')]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $date;

    #[Rule(required: true)]
    #[Json(name: 'muzakki')]
    #[Enum(value: self::MUZAKKI_LIST)]
    private ?int $muzakki;

    #[Rule(required: true)]
    #[Json(name: 'name')]
    private ?string $name;

    #[Rule(required: true)]
    #[Json(name: 'npwz')]
    private ?string $npwz;

    #[Rule(required: true)]
    #[Json(name: 'npwp')]
    private ?string $npwp;

    #[Rule(required: true)]
    #[Json(name: 'address')]
    private ?string $address;

    #[Rule(required: true)]
    #[Json(name: 'phoneNumber')]
    #[Regex(pattern: self::PHONE_NUMBER_PATTERN)]
    private ?string $phoneNumber;

    #[Rule(required: true)]
    #[Json(name: 'amount')]
    private ?int $amount;

    #[Rule(required: true)]
    #[Json(name: 'type')]
    #[Enum(value: self::ZAKAT_TYPE_LIST)]
    private ?int $type;

    #[Rule(required: true)]
    #[Json(name: 'description')]
    private ?string $description;

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $date
     * @param int|null $muzakki
     * @param string|null $name
     * @param string|null $npwz
     * @param string|null $npwp
     * @param string|null $address
     * @param string|null $phoneNumber
     * @param int|null $amount
     * @param int|null $type
     * @param string|null $description
     * @return static
     */
    public function __construct(
        ?DateTimeDecorator $date = null,
        ?int $muzakki = null,
        ?string $name = null,
        ?string $npwz = null,
        ?string $npwp = null,
        ?string $address = null,
        ?string $phoneNumber = null,
        ?int $amount = null,
        ?int $type = null,
        ?string $description = null
    ) {
        $this->setDate($date);
        $this->setMuzakki($muzakki);
        $this->setName($name);
        $this->setNpwz($npwz);
        $this->setNpwp($npwp);
        $this->setAddress($address);
        $this->setPhoneNumber($phoneNumber);
        $this->setAmount($amount);
        $this->setType($type);
        $this->setDescription($description);
    }

    /**
     * @return \Schnell\Decorator\Stringified\DateTimeDecorator
     */
    public function getDate(): ?DateTimeDecorator
    {
        return $this->date;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator $date
     * @return void
     */
    public function setDate(?DateTimeDecorator $date): void
    {
        $this->date = $date;
    }

    /**
     * @return int|null
     */
    public function getMuzakki(): ?int
    {
        return $this->muzakki;
    }

    /**
     * @param int|null $muzakki
     * @return void
     */
    public function setMuzakki(?int $muzakki): void
    {
        $this->muzakki = $muzakki;
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
    public function getNpwz(): ?string
    {
        return $this->npwz;
    }

    /**
     * @param string|null $npwz
     * @return void
     */
    public function setNpwz(?string $npwz): void
    {
        $this->npwz = $npwz;
    }

    /**
     * @return string|null
     */
    public function getNpwp(): ?string
    {
        return $this->npwp;
    }

    /**
     * @param string|null $npwp
     * @return void
     */
    public function setNpwp(?string $npwp): void
    {
        $this->npwp = $npwp;
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
