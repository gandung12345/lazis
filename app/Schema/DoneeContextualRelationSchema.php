<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Rule;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DoneeContextualRelationSchema extends DoneeSerializationAwareSchema
{
    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'id')]
    private ?string $id;

    /**
     * @param string|null $id
     */
    public function __construct(?string $id = null)
    {
        $this->setId($id);
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return void
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }
}
