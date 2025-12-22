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
class ZakatDistributionSchema extends AbstractSchema
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
    #[Json(name: 'program')]
    #[Enum(value: self::ZAKAT_DISTRIBUTION_LIST)]
    private ?int $program;

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
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'proof')]
    private ?string $proof;

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $date
     * @param int|null $program
     * @param int|null $amount
     * @param string|null $description
     * @param string|null $proof
     * @return static
     */
    public function __construct(
        ?DateTimeDecorator $date = null,
        ?int $program = null,
        ?int $amount = null,
        ?string $description = null,
        ?string $proof = null
    ) {
        $this->setDate($date);
        $this->setProgram($program);
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
