<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report\Components;

use RuntimeException;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DsklReception extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'bpkhFund')]
    private int $bpkhFund;

    /**
     * @var int
     */
    #[Json(name: 'qurbanFund')]
    private int $qurbanFund;

    /**
     * @var int
     */
    #[Json(name: 'fidyahFund')]
    private int $fidyahFund;

    public function __construct()
    {
        $this->setBpkhFund(0);
        $this->setQurbanFund(0);
        $this->setFidyahFund(0);
    }

    /**
     * @return int
     */
    public function getBpkhFund(): int
    {
        return $this->bpkhFund;
    }

    /**
     * @param int $bpkhFund
     * @return void
     */
    public function setBpkhFund(int $bpkhFund): void
    {
        $this->bpkhFund = $bpkhFund;
    }

    /**
     * @param int $bpkhFund
     * @return void
     */
    public function addBpkhFund(int $bpkhFund): void
    {
        $this->bpkhFund += $bpkhFund;
    }

    /**
     * @return int
     */
    public function getQurbanFund(): int
    {
        return $this->qurbanFund;
    }

    /**
     * @param int $qurbanFund
     * @return void
     */
    public function setQurbanFund(int $qurbanFund): void
    {
        $this->qurbanFund = $qurbanFund;
    }

    /**
     * @param int $qurbanFund
     * @return void
     */
    public function addQurbanFund(int $qurbanFund): void
    {
        $this->qurbanFund += $qurbanFund;
    }

    /**
     * @return int
     */
    public function getFidyahFund(): int
    {
        return $this->fidyahFund;
    }

    /**
     * @param int $fidyahFund
     * @return void
     */
    public function setFidyahFund(int $fidyahFund): void
    {
        $this->fidyahFund = $fidyahFund;
    }

    /**
     * @param int $fidyahFund
     * @return void
     */
    public function addFidyahFund(int $fidyahFund): void
    {
        $this->fidyahFund += $fidyahFund;
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
