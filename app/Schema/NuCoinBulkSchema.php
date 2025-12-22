<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Rule;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinBulkSchema extends AbstractSchema
{
    #[Json(name: 'donorId')]
    #[Rule(required: true)]
    private ?string $donorId;

    #[Json(name: 'amount')]
    #[Rule(required: true)]
    private ?int $amount;

    #[Json(name: 'date')]
    #[Rule(required: true)]
    private ?DateTimeDecorator $date;

    public function __construct(
        ?string $donorId = null,
        ?int $amount = null,
        ?DateTimeDecorator $date = null
    ) {
        $this->setDonorId($donorId);
        $this->setAmount($amount);
        $this->setDate($date);
    }

    public function getDonorId(): ?string
    {
        return $this->donorId;
    }

    public function setDonorId(?string $donorId): void
    {
        $this->donorId = $donorId;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    public function getDate(): ?DateTimeDecorator
    {
        return $this->date;
    }

    public function setDate(?DateTimeDecorator $date): void
    {
        $this->date = $date;
    }
}
