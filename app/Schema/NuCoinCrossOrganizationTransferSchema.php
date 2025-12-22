<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Regex;
use Schnell\Attribute\Schema\Rule;
use Schnell\Attribute\Schema\TransformedClassType;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinCrossOrganizationTransferSchema extends AbstractSchema
{
    use SchemaTrait;

    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'organizerId')]
    private ?string $organizerId;

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
    #[Json(name: 'status')]
    private ?int $status;

    /**
     * @var int|null
     */
    #[Rule(required: true)]
    #[Json(name: 'type')]
    private ?int $type;

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
    #[Json(name: 'proof')]
    private ?string $proof;

    /**
     * @param string|null $organizerId
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $issuedAt
     * @param string|null $sourceId
     * @param string|null $sourceName
     * @param string|null $destinationId
     * @param string|null $destinationName
     * @param int|null $status
     * @param int|null $type
     * @param int|null $amount
     * @param string|null $proof
     */
    public function __construct(
        ?string $organizerId = null,
        ?DateTimeDecorator $issuedAt = null,
        ?string $sourceId = null,
        ?string $sourceName = null,
        ?string $destinationId = null,
        ?string $destinationName = null,
        ?int $status = null,
        ?int $type = null,
        ?int $amount = null,
        ?string $proof = null
    ) {
        $this->setOrganizerId($organizerId);
        $this->setIssuedAt($issuedAt);
        $this->setSourceId($sourceId);
        $this->setSourceName($sourceName);
        $this->setDestinationId($destinationId);
        $this->setDestinationName($destinationName);
        $this->setStatus($status);
        $this->setType($type);
        $this->setAmount($amount);
        $this->setProof($proof);
    }

    /**
     * @return string|null
     */
    public function getOrganizerId(): ?string
    {
        return $this->organizerId;
    }

    /**
     * @param string|null $organizerId
     * @return void
     */
    public function setOrganizerId(?string $organizerId): void
    {
        $this->organizerId = $organizerId;
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
     * @return string|null $destinationName
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
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     * @return void
     */
    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
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
