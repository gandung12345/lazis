<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report\Components;

use RuntimeException;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DsklDistribution extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'nuSmartFund')]
    private int $nuSmartFund;

    /**
     * @var int
     */
    #[Json(name: 'nuEmpoweredFund')]
    private int $nuEmpoweredFund;

    /**
     * @var int
     */
    #[Json(name: 'nuHealthFund')]
    private int $nuHealthFund;

    /**
     * @var int
     */
    #[Json(name: 'nuGreenFund')]
    private int $nuGreenFund;

    /**
     * @var int
     */
    #[Json(name: 'nuPeaceFund')]
    private int $nuPeaceFund;

    /**
     * @var int
     */
    #[Json(name: 'nuPeaceWithQurbanFund')]
    private int $nuPeaceWithQurbanFund;

    /**
     * @var int
     */
    #[Json(name: 'amilAllocationFund')]
    private int $amilAllocationFund;

    public function __construct()
    {
        $this->setNuSmartFund(0);
        $this->setNuEmpoweredFund(0);
        $this->setNuHealthFund(0);
        $this->setNuGreenFund(0);
        $this->setNuPeaceFund(0);
        $this->setNuPeaceWithQurbanFund(0);
        $this->setAmilAllocationFund(0);
    }

    /**
     * @return int
     */
    public function getNuSmartFund(): int
    {
        return $this->nuSmartFund;
    }

    /**
     * @param int $nuSmartFund
     * @return void
     */
    public function setNuSmartFund(int $nuSmartFund): void
    {
        $this->nuSmartFund = $nuSmartFund;
    }

    /**
     * @param int $nuSmartFund
     * @return void
     */
    public function addNuSmartFund(int $nuSmartFund): void
    {
        $this->nuSmartFund += $nuSmartFund;
    }

    /**
     * @return int
     */
    public function getNuEmpoweredFund(): int
    {
        return $this->nuEmpoweredFund;
    }

    /**
     * @param int $nuEmpoweredFund
     * @return void
     */
    public function setNuEmpoweredFund(int $nuEmpoweredFund): void
    {
        $this->nuEmpoweredFund = $nuEmpoweredFund;
    }

    /**
     * @param int $nuEmpoweredFund
     * @return void
     */
    public function addNuEmpoweredFund(int $nuEmpoweredFund): void
    {
        $this->nuEmpoweredFund += $nuEmpoweredFund;
    }

    /**
     * @return int
     */
    public function getNuHealthFund(): int
    {
        return $this->nuHealthFund;
    }

    /**
     * @param int $nuHealthFund
     * @return void
     */
    public function setNuHealthFund(int $nuHealthFund): void
    {
        $this->nuHealthFund = $nuHealthFund;
    }

    /**
     * @param int $nuHealthFund
     * @return void
     */
    public function addNuHealthFund(int $nuHealthFund): void
    {
        $this->nuHealthFund += $nuHealthFund;
    }

    /**
     * @return int
     */
    public function getNuGreenFund(): int
    {
        return $this->nuGreenFund;
    }

    /**
     * @param int $nuGreenFund
     * @return void
     */
    public function setNuGreenFund(int $nuGreenFund): void
    {
        $this->nuGreenFund = $nuGreenFund;
    }

    /**
     * @param int $nuGreenFund
     * @return void
     */
    public function addNuGreenFund(int $nuGreenFund): void
    {
        $this->nuGreenFund += $nuGreenFund;
    }

    /**
     * @return int
     */
    public function getNuPeaceFund(): int
    {
        return $this->nuPeaceFund;
    }

    /**
     * @param int $nuPeaceFund
     * @return void
     */
    public function setNuPeaceFund(int $nuPeaceFund): void
    {
        $this->nuPeaceFund = $nuPeaceFund;
    }

    /**
     * @param int $nuPeaceFund
     * @return void
     */
    public function addNuPeaceFund(int $nuPeaceFund): void
    {
        $this->nuPeaceFund += $nuPeaceFund;
    }

    /**
     * @return int
     */
    public function getNuPeaceWithQurbanFund(): int
    {
        return $this->nuPeaceWithQurbanFund;
    }

    /**
     * @param int $nuPeaceWithQurbanFund
     * @return void
     */
    public function setNuPeaceWithQurbanFund(int $nuPeaceWithQurbanFund): void
    {
        $this->nuPeaceWithQurbanFund = $nuPeaceWithQurbanFund;
    }

    /**
     * @param int $nuPeaceWithQurbanFund
     * @return void
     */
    public function addNuPeaceWithQurbanFund(int $nuPeaceWithQurbanFund): void
    {
        $this->nuPeaceWithQurbanFund += $nuPeaceWithQurbanFund;
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
