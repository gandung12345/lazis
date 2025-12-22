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
class NuCoinCrossTransferQueueSchema extends AbstractSchema
{
    use SchemaTrait;

    /**
     * @var \Schnell\Decorator\Stringified\DateTimeDecorator|null
     */
    #[Rule(required: true)]
    #[Json(name: 'issuedAt')]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $issuedAt;

    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'sourceId')]
    private ?string $sourceId;

    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'sourceName')]
    private ?string $sourceName;

    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'destinationId')]
    private ?string $destinationId;

    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'destinationName')]
    private ?string $destinationName;

    /**
     * @var int|null
     */
    #[Rule(required: true)]
    #[Json(name: 'amount')]
    private ?int $amount;

    /**
     * @var int|null
     */
    #[Rule(required: true)]
    #[Json(name: 'transferAmount')]
    private ?int $transferAmount;

    /**
     * @var int|null
     */
    #[Rule(required: true)]
    #[Json(name: 'status')]
    #[Enum(value: self::NU_COIN_CROSS_TRANSFER_STATUS_LIST)]
    private ?int $status;

    /**
     * @var int|null
     */
    #[Rule(required: true)]
    #[Json(name: 'type')]
    #[Enum(value: self::NU_COIN_CROSS_TRANSFER_QUEUE_STATUS_LIST)]
    private ?int $type;

    /**
     * @var string|null
     */
    #[Rule(required: false)]
    #[Json(name: 'proof')]
    private ?string $proof;

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $issuedAt
     * @param string|null $sourceId
     * @param string|null $sourceName
     * @param string|null $destinationId
     * @param string|null $destinationName
     * @param int|null $amount
     * @param int|null $transferAmount
     * @param int|null $status
     * @param int|null $type
     * @param string|null $proof
     */
    public function __construct(
        ?DateTimeDecorator $issuedAt = null,
        ?string $sourceId = null,
        ?string $sourceName = null,
        ?string $destinationId = null,
        ?string $destinationName = null,
        ?int $amount = null,
        ?int $transferAmount = null,
        ?int $status = null,
        ?int $type = null,
        ?string $proof = null
    ) {
        $this->setIssuedAt($issuedAt);
        $this->setSourceId($sourceId);
        $this->setSourceName($sourceName);
        $this->setDestinationId($destinationId);
        $this->setDestinationName($destinationName);
        $this->setAmount($amount);
        $this->setTransferAmount($transferAmount);
        $this->setStatus($status);
        $this->setType($type);
        $this->setProof($proof);
    }

    /**
     * @return \Schnell\Decorator\Stringified\DateTimeDecorator|null
     */
    public function getIssuedAt(): ?DateTimeDecorator
    {
        return $this->issuedAt;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $issuedAt
     * @return void
     */
    public function setIssuedAt(?DateTimeDecorator $issuedAt): void
    {
        $this->issuedAt = $issuedAt;
    }

    /**
     * @return string|null
     */
    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

    /**
     * @param string|null $sourceId
     * @return void
     */
    public function setSourceId(?string $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    /**
     * @return string|null
     */
    public function getSourceName(): ?string
    {
        return $this->sourceName;
    }

    /**
     * @param string|null $sourceName
     * @return void
     */
    public function setSourceName(?string $sourceName): void
    {
        $this->sourceName = $sourceName;
    }

    /**
     * @return string|null
     */
    public function getDestinationId(): ?string
    {
        return $this->destinationId;
    }

    /**
     * @param string|null $destinationId
     * @return void
     */
    public function setDestinationId(?string $destinationId): void
    {
        $this->destinationId = $destinationId;
    }

    /**
     * @return string|null
     */
    public function getDestinationName(): ?string
    {
        return $this->destinationName;
    }

    /**
     * @param string|null $destinationName
     * @return void
     */
    public function setDestinationName(?string $destinationName): void
    {
        $this->destinationName = $destinationName;
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
    public function getTransferAmount(): ?int
    {
        return $this->transferAmount;
    }

    /**
     * @param int|null $transferAmount
     * @return void
     */
    public function setTransferAmount(?int $transferAmount): void
    {
        $this->transferAmount = $transferAmount;
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
