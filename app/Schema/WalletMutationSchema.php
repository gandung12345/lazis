<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Enum;
use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Regex;
use Schnell\Attribute\Schema\Rule;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class WalletMutationSchema extends AbstractSchema
{
    use SchemaTrait;

    /**
     * @var int|null
     */
    #[Rule(required: true)]
    #[Json(name: 'type')]
    #[Enum(value: self::WALLET_TYPE_LIST)]
    private ?int $type;

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
    #[Json(name: 'year')]
    private ?int $year;

    /**
     * @param int|null $type
     * @param int|null $amount
     * @param int|null $year
     */
    public function __construct(
        ?int $type = null,
        ?int $amount = null,
        ?int $year = null
    ) {
        $this->setType($type);
        $this->setAmount($amount);
        $this->setYear($year);
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
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int|null $year
     * @return void
     */
    public function setYear(?int $year): void
    {
        $this->year = $year;
    }
}
