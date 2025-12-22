<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report\Components;

use RuntimeException;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class AsnafBasedDistribution extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'fakirFund')]
    private int $fakirFund;

    /**
     * @var int
     */
    #[Json(name: 'poorFund')]
    private int $poorFund;

    /**
     * @var int
     */
    #[Json(name: 'fisabilillahFund')]
    private int $fisabilillahFund;

    /**
     * @var int
     */
    #[Json(name: 'ibnuSabilFund')]
    private int $ibnuSabilFund;

    /**
     * @var int
     */
    #[Json(name: 'gharimFund')]
    private int $gharimFund;

    /**
     * @var int
     */
    #[Json(name: 'mualafFund')]
    private int $mualafFund;

    /**
     * @var int
     */
    #[Json(name: 'riqabFund')]
    private int $riqabFund;

    /**
     * @var int
     */
    #[Json(name: 'amilAllocationFund')]
    private int $amilAllocationFund;

    public function __construct()
    {
        $this->setFakirFund(0);
        $this->setPoorFund(0);
        $this->setFisabilillahFund(0);
        $this->setIbnuSabilFund(0);
        $this->setGharimFund(0);
        $this->setMualafFund(0);
        $this->setRiqabFund(0);
        $this->setAmilAllocationFund(0);
    }

    /**
     * @return int
     */
    public function getFakirFund(): int
    {
        return $this->fakirFund;
    }

    /**
     * @param int $fakirFund
     * @return void
     */
    public function setFakirFund(int $fakirFund): void
    {
        $this->fakirFund = $fakirFund;
    }

    /**
     * @param int $fakirFund
     * @return void
     */
    public function addFakirFund(int $fakirFund): void
    {
        $this->fakirFund += $fakirFund;
    }

    /**
     * @return int
     */
    public function getPoorFund(): int
    {
        return $this->poorFund;
    }

    /**
     * @param int $poorFund
     * @return void
     */
    public function setPoorFund(int $poorFund): void
    {
        $this->poorFund = $poorFund;
    }

    /**
     * @param int $poorFund
     * @return void
     */
    public function addPoorFund(int $poorFund): void
    {
        $this->poorFund += $poorFund;
    }

    /**
     * @return int
     */
    public function getFisabilillahFund(): int
    {
        return $this->fisabilillahFund;
    }

    /**
     * @param int $fisabilillahFund
     * @return void
     */
    public function setFisabilillahFund(int $fisabilillahFund): void
    {
        $this->fisabilillahFund = $fisabilillahFund;
    }

    /**
     * @param int $fisabilillahFund
     * @return void
     */
    public function addFisabilillahFund(int $fisabilillahFund): void
    {
        $this->fisabilillahFund += $fisabilillahFund;
    }

    /**
     * @return int
     */
    public function getIbnuSabilFund(): int
    {
        return $this->ibnuSabilFund;
    }

    /**
     * @param int $ibnuSabilFund
     * @return void
     */
    public function setIbnuSabilFund(int $ibnuSabilFund): void
    {
        $this->ibnuSabilFund = $ibnuSabilFund;
    }

    /**
     * @param int $ibnuSabilFund
     * @return void
     */
    public function addIbnuSabilFund(int $ibnuSabilFund): void
    {
        $this->ibnuSabilFund += $ibnuSabilFund;
    }

    /**
     * @return int
     */
    public function getGharimFund(): int
    {
        return $this->gharimFund;
    }

    /**
     * @param int $gharimFund
     * @return void
     */
    public function setGharimFund(int $gharimFund): void
    {
        $this->gharimFund = $gharimFund;
    }

    /**
     * @param int $gharimFund
     * @return void
     */
    public function addGharimFund(int $gharimFund): void
    {
        $this->gharimFund += $gharimFund;
    }

    /**
     * @return int
     */
    public function getMualafFund(): int
    {
        return $this->mualafFund;
    }

    /**
     * @param int $mualafFund
     * @return void
     */
    public function setMualafFund(int $mualafFund): void
    {
        $this->mualafFund = $mualafFund;
    }

    /**
     * @param int $mualafFund
     * @return void
     */
    public function addMualafFund(int $mualafFund): void
    {
        $this->mualafFund += $mualafFund;
    }

    /**
     * @return int
     */
    public function getRiqabFund(): int
    {
        return $this->riqabFund;
    }

    /**
     * @param int $riqabFund
     * @return void
     */
    public function setRiqabFund(int $riqabFund): void
    {
        $this->riqabFund = $riqabFund;
    }

    /**
     * @param int $riqabFund
     * @return void
     */
    public function addRiqabFund(int $riqabFund): void
    {
        $this->riqabFund += $riqabFund;
    }

    /**
     * @return int
     */
    public function getAmilAllocationFund(): int
    {
        return $this->amilAllocationFund;
    }

    /**
     * @param int $amilAllocationFund
     * @return void
     */
    public function setAmilAllocationFund(int $amilAllocationFund): void
    {
        $this->amilAllocationFund = $amilAllocationFund;
    }

    /**
     * @param int $amilAllocationFund
     * @return void
     */
    public function addAmilAllocationFund(int $amilAllocationFund): void
    {
        $this->amilAllocationFund += $amilAllocationFund;
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
