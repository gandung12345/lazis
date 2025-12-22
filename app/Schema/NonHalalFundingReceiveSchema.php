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
class NonHalalFundingReceiveSchema extends AbstractSchema
{
    use SchemaTrait;

    #[Rule(required: true)]
    #[Json(name: 'date')]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $date;

    #[Rule(required: true)]
    #[Json(name: 'kind')]
    #[Enum(value: self::NON_HALAL_FUNDING_RECEIVE_LIST)]
    private ?int $kind;

    #[Rule(required: true)]
    #[Json(name: 'amount')]
    private ?int $amount;

    #[Rule(required: true)]
    #[Json(name: 'description')]
    private ?string $description;

    #[Rule(required: true)]
    #[Json(name: 'proof')]
    private ?string $proof;

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $date
     * @param int|null $kind
     * @param int|null $amount
     * @param string|null $description
     * @param string|null $proof
     */
    public function __construct(
        ?DateTimeDecorator $date = null,
        ?int $kind = null,
        ?int $amount = null,
        ?string $description = null,
        ?string $proof = null
    ) {
        $this->setDate($date);
        $this->setKind($kind);
        $this->setAmount($amount);
        $this->setDescription($description);
        $this->setProof($proof);
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
}
