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
class AssetRecordingSchema extends AbstractSchema
{
    use SchemaTrait;

    /**
     * @var int|null
     */
    #[Json(name: 'kind')]
    #[Rule(required: true)]
    #[Enum(value: self::ASSET_RECORDING_LIST)]
    private ?int $kind;

    /**
     * @var string|null
     */
    #[Json(name: 'name')]
    #[Rule(required: true)]
    private ?string $name;

    /**
     * @var \Schnell\Decorator\Stringified\DateTimeDecorator|null
     */
    #[Json(name: 'date')]
    #[Rule(required: true)]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $date;

    /**
     * @var int|null
     */
    #[Json(name: 'price')]
    #[Rule(required: true)]
    private ?int $price;

    /**
     * @param int|null $kind
     * @param string|null $name
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $date
     * @param int|null $price
     */
    public function __construct(
        ?int $kind = null,
        ?string $name = null,
        ?DateTimeDecorator $date = null,
        ?int $price = null
    ) {
        $this->setKind($kind);
        $this->setName($name);
        $this->setDate($date);
        $this->setPrice($price);
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
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param int|null $price
     * @return void
     */
    public function setPrice(?int $price): void
    {
        $this->price = $price;
    }
}
