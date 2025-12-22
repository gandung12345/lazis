<?php

declare(strict_types=1);

namespace Lazis\Api\Schema\Report;

use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Rule;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ReportNonHalalFundingChangeSchema extends AbstractSchema
{
    /**
     * @var string|null
     */
    #[Json(name: 'organizationId')]
    #[Rule(required: true)]
    private ?string $organizationId;

    /**
     * @var \Lazis\Api\Schema\Report\ReportYearRangeSchema|null
     */
    #[Json(name: '@year')]
    #[Rule(required: true)]
    private ?ReportYearRangeSchema $year;

    /**
     * @param string|null $organizationId
     * @param \Lazis\Api\Schema\Report\ReportYearRangeSchema|null $year
     */
    public function __construct(
        ?string $organizationId = null,
        ?ReportYearRangeSchema $year = null
    ) {
        $this->setOrganizationId($organizationId);
        $this->setYear($year);
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

    /**
     * @return \Lazis\Api\Schema\Report\ReportYearRangeSchema|null
     */
    public function getYear(): ?ReportYearRangeSchema
    {
        return $this->year;
    }

    /**
     * @param \Lazis\Api\Schema\Report\ReportYearRangeSchema|null $year
     * @return void
     */
    public function setYear(?ReportYearRangeSchema $year): void
    {
        $this->year = $year;
    }
}
