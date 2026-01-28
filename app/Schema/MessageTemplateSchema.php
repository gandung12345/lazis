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
class MessageTemplateSchema extends AbstractSchema
{
    use SchemaTrait;

    #[Json(name: 'type')]
    #[Rule(required: true)]
    #[Enum(value: self::MESSAGE_TEMPLATE_TYPE_LIST)]
    private ?int $type;

    #[Json(name: 'message')]
    #[Rule(required: true)]
    private ?string $message;

    public function __construct(?int $type = null, ?string $message = null)
    {
        $this->setType($type);
        $this->setMessage($message);
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): void
    {
        $this->type = $type;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }
}
