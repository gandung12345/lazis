<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Rule;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DashboardSchema extends AbstractSchema
{
    #[Rule(required: true)]
    #[Json(name: 'organizationId')]
    private ?string $organizationId;

    /**
     * @param string|null $organizationId
     * @return static
     */
    public function __construct(?string $organizationId = null)
    {
        $this->setOrganizationId($organizationId);
    }

    /**
     * @return string|null
     */
    public function getOrganizationId(): ?string
    {
        return $this->organizationId;
    }

    /**
     * @param string|null $organizationId
     * @return void
     */
    public function setOrganizationId(?string $organizationId): void
    {
        $this->organizationId = $organizationId;
    }
}
