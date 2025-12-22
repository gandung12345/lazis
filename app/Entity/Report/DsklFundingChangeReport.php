<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report;

use RuntimeException;
use Lazis\Api\Entity\Report\Components\DsklReception;
use Lazis\Api\Entity\Report\Components\DsklDistribution;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DsklFundingChangeReport extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'year')]
    private int $year;

    /**
     * @var \Lazis\Api\Entity\Report\Components\DsklReception
     */
    #[Json(name: 'dsklReception')]
    private DsklReception $dsklReception;

    /**
     * @var \Lazis\Api\Entity\Report\Components\DsklDistribution
     */
    #[Json(name: 'dsklDistribution')]
    private DsklDistribution $dsklDistribution;

    /**
     * @param int $year
     * @param \Lazis\Api\Entity\Report\Components\DsklReception|null $dsklReception
     * @param \Lazis\Api\Entity\Report\Components\DsklDistribution|null $dsklDistribution
     */
    public function __construct(
        int $year = 0,
        ?DsklReception $dsklReception = null,
        ?DsklDistribution $dsklDistribution = null
    ) {
        $this->setYear($year);
        $this->setDsklReception($dsklReception ?? new DsklReception());
        $this->setDsklDistribution($dsklDistribution ?? new DsklDistribution());
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     * @return void
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return \Lazis\Api\Entity\Report\Components\DsklReception
     */
    public function getDsklReception(): DsklReception
    {
        return $this->dsklReception;
    }

    /**
     * @param \Lazis\Api\Entity\Report\Components\DsklReception $dsklReception
     * @return void
     */
    public function setDsklReception(DsklReception $dsklReception): void
    {
        $this->dsklReception = $dsklReception;
    }

    /**
     * @return \Lazis\Api\Entity\Report\Components\DsklDistribution
     */
    public function getDsklDistribution(): DsklDistribution
    {
        return $this->dsklDistribution;
    }

    /**
     * @param \Lazis\Api\Entity\Report\Components\DsklDistribution $dsklDistribution
     * @return void
     */
    public function setDsklDistribution(DsklDistribution $dsklDistribution): void
    {
        $this->dsklDistribution = $dsklDistribution;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        throw new RuntimeException("Not implemented.");
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        throw new RuntimeException("Not implemented.");
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        throw new RuntimeException("Not implemented.");
    }
}
