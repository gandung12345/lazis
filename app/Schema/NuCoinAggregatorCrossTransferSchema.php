<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Rule;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinAggregatorCrossTransferSchema extends AbstractSchema
{
    use SchemaTrait;

    #[Json(name: 'organizationId')]
    #[Rule(required: true)]
    private ?string $organizationId;

    public function __construct(?string $organizationId = null)
    {
        $this->setOrganizationId($organizationId);
    }

    public function getOrganizationId(): ?string
    {
        return $this->organizationId;
    }

    public function setOrganizationId(?string $organizationId): void
    {
        $this->organizationId = $organizationId;
    }
}
