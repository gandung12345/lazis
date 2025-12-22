<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report\Components;

use RuntimeException;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NonHalalDistribution extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'bankAdmin')]
    private int $bankAdmin;

    /**
     * @var int
     */
    #[Json(name: 'nonHalalFundingUsage')]
    private int $nonHalalFundingUsage;

    public function __construct()
    {
        $this->setBankAdmin(0);
        $this->setNonHalalFundingUsage(0);
    }

    /**
     * @return int
     */
    public function getBankAdmin(): int
    {
        return $this->bankAdmin;
    }

    /**
     * @param int $bankAdmin
     * @return void
     */
    public function setBankAdmin(int $bankAdmin): void
    {
        $this->bankAdmin = $bankAdmin;
    }

    /**
     * @param int $bankAdmin
     * @return void
     */
    public function addBankAdmin(int $bankAdmin): void
    {
        $this->bankAdmin += $bankAdmin;
    }

    /**
     * @return int
     */
    public function getNonHalalFundingUsage(): int
    {
        return $this->nonHalalFundingUsage;
    }

    /**
     * @param int $nonHalalFundingUsage
     * @return void
     */
    public function setNonHalalFundingUsage(int $nonHalalFundingUsage): void
    {
        $this->nonHalalFundingUsage = $nonHalalFundingUsage;
    }

    /**
     * @param int $nonHalalFundingUsage
     * @return void
     */
    public function addNonHalalFundingUsage(int $nonHalalFundingUsage): void
    {
        $this->nonHalalFundingUsage += $nonHalalFundingUsage;
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
