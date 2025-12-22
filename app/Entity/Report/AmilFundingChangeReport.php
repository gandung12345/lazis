<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report;

use RuntimeException;
use Lazis\Api\Entity\Report\Components\AmilFundingReception;
use Lazis\Api\Entity\Report\Components\AmilFundingUtilization;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class AmilFundingChangeReport extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'year')]
    private int $year;

    /**
     * @var \Lazis\Api\Entity\Report\Components\AmilFundingReception
     */
    #[Json(name: 'amilFundingReception')]
    private AmilFundingReception $amilFundingReception;

    /**
     * @var \Lazis\Api\Entity\Report\Components\AmilFundingUtilization
     */
    #[Json(name: 'amilFundingUtilization')]
    private AmilFundingUtilization $amilFundingUtilization;

    /**
     * @param int $year
     * @param \Lazis\Api\Entity\Report\Components\AmilFundingReception $amilFundingReception
     * @param \Lazis\Api\Entity\Report\Components\AmilFundingUtilization $amilFundingUtilization
     */
    public function __construct(
        int $year = 0,
        ?AmilFundingReception $amilFundingReception = null,
        ?AmilFundingUtilization $amilFundingUtilization = null
    ) {
        $this->setYear($year);
        $this->setAmilFundingReception($amilFundingReception ?? new AmilFundingReception());
        $this->setAmilFundingUtilization(
            $amilFundingUtilization ?? new AmilFundingUtilization()
        );
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
     * @return \Lazis\Api\Entity\Report\Components\AmilFundingReception
     */
    public function getAmilFundingReception(): AmilFundingReception
    {
        return $this->amilFundingReception;
    }

    /**
     * @param \Lazis\Api\Entity\Report\Components\AmilFundingReception $amilFundingReception
     * @return void
     */
    public function setAmilFundingReception(AmilFundingReception $amilFundingReception): void
    {
        $this->amilFundingReception = $amilFundingReception;
    }

    /**
     * @return \Lazis\Api\Entity\Report\Components\AmilFundingUtilization
     */
    public function getAmilFundingUtilization(): AmilFundingUtilization
    {
        return $this->amilFundingUtilization;
    }

    /**
     * @param \Lazis\Api\Entity\Report\Components\AmilFundingUtilization $amilFundingUtilization
     * @return void
     */
    public function setAmilFundingUtilization(
        AmilFundingUtilization $amilFundingUtilization
    ): void {
        $this->amilFundingUtilization = $amilFundingUtilization;
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
