<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report;

use RuntimeException;
use Lazis\Api\Entity\Report\Components\ZakatReception;
use Lazis\Api\Entity\Report\Components\AsnafBasedDistribution;
use Lazis\Api\Entity\Report\Components\ProgramBasedDistribution;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ZakatFundingChangeReport extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'year')]
    private int $year;

    /**
     * @var \Lazis\Api\Entity\Report\Components\ZakatReception
     */
    #[Json(name: 'zakatReception')]
    private ZakatReception $zakatReception;

    /**
     * @var \Lazis\Api\Entity\Report\Components\AsnafBasedDistribution
     */
    #[Json(name: 'AsnafBasedDistribution')]
    private AsnafBasedDistribution $asnafBasedDistribution;

    /**
     * @var \Lazis\Api\Entity\Report\Components\ProgramBasedDistribution
     */
    #[Json(name: 'programBasedDistribution')]
    private ProgramBasedDistribution $programBasedDistribution;

    /**
     * @param int $year
     * @param \Lazis\Api\Entity\Report\Components\ZakatReception|null $zakatReception
     * @param \Lazis\Api\Entity\Report\Components\AsnafBasedDistribution|null $asnafDist
     * @param \Lazis\Api\Entity\Report\Components\ProgramBasedDistribution|null $programDist
     */
    public function __construct(
        int $year = 0,
        ?ZakatReception $zakatReception = null,
        ?AsnafBasedDistribution $asnafDist = null,
        ?ProgramBasedDistribution $programDist = null
    ) {
        $this->setYear($year);
        $this->setZakatReception($zakatReception ?? new ZakatReception());
        $this->setAsnafBasedDistribution($asnafDist ?? new AsnafBasedDistribution());
        $this->setProgramBasedDistribution($programDist ?? new ProgramBasedDistribution());
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
     * @return \Lazis\Api\Entity\Report\Components\ZakatReception
     */
    public function getZakatReception(): ZakatReception
    {
        return $this->zakatReception;
    }

    /**
     * @param \Lazis\Api\Entity\Report\Components\ZakatReception $zakatReception
     * @return void
     */
    public function setZakatReception(ZakatReception $zakatReception): void
    {
        $this->zakatReception = $zakatReception;
    }

    /**
     * @return \Lazis\Api\Entity\Report\Components\AsnafBasedDistribution
     */
    public function getAsnafBasedDistribution(): AsnafBasedDistribution
    {
        return $this->asnafBasedDistribution;
    }

    /**
     * @param \Lazis\Api\Entity\Report\Components\AsnafBasedDistribution $asnafDist
     * @return void
     */
    public function setAsnafBasedDistribution(AsnafBasedDistribution $asnafDist): void
    {
        $this->asnafBasedDistribution = $asnafDist;
    }

    /**
     * @return \Lazis\Api\Entity\Report\Components\ProgramBasedDistribution
     */
    public function getProgramBasedDistribution(): ProgramBasedDistribution
    {
        return $this->programBasedDistribution;
    }

    /**
     * @param \Lazis\Api\Entity\Report\Components\ProgramBasedDistribution $programDist
     * @return void
     */
    public function setProgramBasedDistribution(ProgramBasedDistribution $programDist): void
    {
        $this->programBasedDistribution = $programDist;
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
