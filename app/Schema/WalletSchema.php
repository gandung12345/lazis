<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Enum;
use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Rule;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class WalletSchema extends AbstractSchema
{
    use SchemaTrait;

    #[Rule(required: true)]
    #[Json(name: 'type')]
    #[Enum(value: self::WALLET_TYPE_LIST)]
    private ?int $type;

    /**
     * @param int|null $type
     * @return static
     */
    public function __construct(?int $type = null)
    {
        $this->setType($type);
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
}
