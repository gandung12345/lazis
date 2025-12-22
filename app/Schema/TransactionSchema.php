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
class TransactionSchema extends AbstractSchema
{
    use SchemaTrait;

    #[Json(name: 'date')]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[Rule(required: true)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $date;

    #[Json(name: 'amount')]
    #[Rule(required: true)]
    private ?int $amount;

    #[Json(name: 'type')]
    #[Rule(required: true)]
    #[Enum(value: self::TRANSACTION_LIST)]
    private ?int $type;

    #[Json(name: 'description')]
    #[Rule(required: true)]
    private ?string $description;

    #[Json(name: '@wallet')]
    #[Rule(required: true)]
    private ?WalletSchema $wallet;

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $date
     * @param int|null $amount
     * @param int|null $type
     * @param string|null $description
     * @param \Lazis\Api\Schema\WalletSchema|null $wallet
     * @return static
     */
    public function __construct(
        ?DateTimeDecorator $date = null,
        ?int $amount = null,
        ?int $type = null,
        ?string $description = null,
        ?WalletSchema $wallet = null
    ) {
        $this->setDate($date);
        $this->setAmount($amount);
        $this->setType($type);
        $this->setDescription($description);
        $this->setWallet($wallet);
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

    /**
     * @return \Lazis\Api\Schema\WalletSchema|null
     */
    public function getWallet(): ?WalletSchema
    {
        return $this->wallet;
    }

    /**
     * @param \Lazis\Api\Schema\WalletSchema|null $wallet
     * @return void
     */
    public function setWallet(?WalletSchema $wallet): void
    {
        $this->wallet = $wallet;
    }
}
