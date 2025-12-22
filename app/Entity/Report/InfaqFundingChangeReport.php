<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report;

use RuntimeException;
use Lazis\Api\Entity\Report\Components\InfaqDistribution;
use Lazis\Api\Entity\Report\Components\InfaqReception;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class InfaqFundingChangeReport extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'year')]
    private int $year;

    /**
     * @var int
     */
    #[Json(name: 'infaqReception')]
    private InfaqReception $infaqReception;

    /**
     * @var int
     */
    #[Json(name: 'infaqDistribution')]
    private InfaqDistribution $infaqDistribution;

    public function __construct(
        int $year = 0,
        ?InfaqReception $infaqReception = null,
        ?InfaqDistribution $infaqDistribution = null
    ) {
        $this->setYear(0);
        $this->setInfaqReception($infaqReception ?? new InfaqReception());
        $this->setInfaqDistribution($infaqDistribution ?? new InfaqDistribution());
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
     * @return \Lazis\Api\Entity\Report\Components\InfaqReception
     */
    public function getInfaqReception(): InfaqReception
    {
        return $this->infaqReception;
    }

    /**
     * @param \Lazis\Api\Entity\Report\Components\InfaqReception $infaqReception
     * @return void
     */
    public function setInfaqReception(InfaqReception $infaqReception): void
    {
        $this->infaqReception = $infaqReception;
    }

    /**
     * @return \Lazis\Api\Entity\Report\Components\InfaqDistribution
     */
    public function getInfaqDistribution(): InfaqDistribution
    {
        return $this->infaqDistribution;
    }

    /**
     * @param \Lazis\Api\Entity\Report\Components\InfaqDistribution $infaqDistribution
     * @return void
     */
    public function setInfaqDistribution(InfaqDistribution $infaqDistribution): void
    {
        $this->infaqDistribution = $infaqDistribution;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        throw new RuntimeException("Not implemented");
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        throw new RuntimeException("Not implemented");
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        throw new RuntimeException("Not implemented");
    }
}
