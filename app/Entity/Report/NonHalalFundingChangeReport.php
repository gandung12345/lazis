<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report;

use RuntimeException;
use Lazis\Api\Entity\Report\Components\NonHalalDistribution;
use Lazis\Api\Entity\Report\Components\NonHalalReception;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NonHalalFundingChangeReport extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'year')]
    private int $year;

    /**
     * @var \Lazis\Api\Entity\Report\Components\NonHalalReception
     */
    #[Json(name: 'nonHalalReception')]
    private NonHalalReception $nonHalalReception;

    /**
     * @var \Lazis\Api\Entity\Report\Components\NonHalalDistribution
     */
    #[Json(name: 'nonHalalDistribution')]
    private NonHalalDistribution $nonHalalDistribution;

    /**
     * @param \Lazis\Api\Entity\Report\Components\NonHalalReception|null $nonHalalReception
     * @param \Lazis\Api\Entity\Report\Components\NonHalalDistribution|null $nonHalalDistribution
     */
    public function __construct(
        ?NonHalalReception $nonHalalReception = null,
        ?NonHalalDistribution $nonHalalDistribution = null
    ) {
        $this->setYear(0);
        $this->setNonHalalReception($nonHalalReception ?? new NonHalalReception());
        $this->setNonHalalDistribution($nonHalalDistribution ?? new NonHalalDistribution());
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
     * @return \Lazis\Api\Entity\Report\Components\NonHalalReception
     */
    public function getNonHalalReception(): NonHalalReception
    {
        return $this->nonHalalReception;
    }

    /**
     * @param \Lazis\Api\Entity\Report\Components\NonHalalReception $nonHalalReception
     * @return void
     */
    public function setNonHalalReception(NonHalalReception $nonHalalReception): void
    {
        $this->nonHalalReception = $nonHalalReception;
    }

    /**
     * @return \Lazis\Api\Entity\Report\Components\NonHalalDistribution
     */
    public function getNonHalalDistribution(): NonHalalDistribution
    {
        return $this->nonHalalDistribution;
    }

    /**
     * @param \Lazis\Api\Entity\Report\Components\NonHalalDistribution $nonHalalDistribution
     * @return void
     */
    public function setNonHalalDistribution(NonHalalDistribution $nonHalalDistribution): void
    {
        $this->nonHalalDistribution = $nonHalalDistribution;
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
