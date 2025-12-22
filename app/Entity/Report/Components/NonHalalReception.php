<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report\Components;

use RuntimeException;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NonHalalReception extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'bankInterestFund')]
    private int $bankInterestFund;

    /**
     * @var int
     */
    #[Json(name: 'currentAccountServiceFund')]
    private int $currentAccountServiceFund;

    /**
     * @var int
     */
    #[Json(name: 'otherFund')]
    private int $otherFund;

    public function __construct()
    {
        $this->setBankInterestFund(0);
        $this->setCurrentAccountServiceFund(0);
        $this->setOtherFund(0);
    }

    /**
     * @return int
     */
    public function getBankInterestFund(): int
    {
        return $this->bankInterestFund;
    }

    /**
     * @param int $bankInterestFund
     * @return void
     */
    public function setBankInterestFund(int $bankInterestFund): void
    {
        $this->bankInterestFund = $bankInterestFund;
    }

    /**
     * @param int $bankInterestFund
     * @return void
     */
    public function addBankInterestFund(int $bankInterestFund): void
    {
        $this->bankInterestFund += $bankInterestFund;
    }

    /**
     * @return int
     */
    public function getCurrentAccountServiceFund(): int
    {
        return $this->currentAccountServiceFund;
    }

    /**
     * @param int $currentAccountServiceFund
     * @return void
     */
    public function setCurrentAccountServiceFund(int $currentAccountServiceFund): void
    {
        $this->currentAccountServiceFund = $currentAccountServiceFund;
    }

    /**
     * @param int $currentAccountServiceFund
     * @return void
     */
    public function addCurrentAccountServiceFund(int $currentAccountServiceFund): void
    {
        $this->currentAccountServiceFund += $currentAccountServiceFund;
    }

    /**
     * @return int
     */
    public function getOtherFund(): int
    {
        return $this->otherFund;
    }

    /**
     * @param int $otherFund
     * @return void
     */
    public function setOtherFund(int $otherFund): void
    {
        $this->otherFund = $otherFund;
    }

    /**
     * @param int $otherFund
     * @return void
     */
    public function addOtherFund(int $otherFund): void
    {
        $this->otherFund += $otherFund;
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
