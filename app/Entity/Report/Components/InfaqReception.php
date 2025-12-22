<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report\Components;

use RuntimeException;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class InfaqReception extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'boundedFund')]
    private int $boundedFund;

    /**
     * @var int
     */
    #[Json(name: 'unboundedFund')]
    private int $unboundedFund;

    /**
     * @var int
     */
    #[Json(name: 'nuCoinFund')]
    private int $nuCoinFund;

    public function __construct()
    {
        $this->setBoundedFund(0);
        $this->setUnboundedFund(0);
        $this->setNuCoinFund(0);
    }

    /**
     * @return int
     */
    public function getBoundedFund(): int
    {
        return $this->boundedFund;
    }

    /**
     * @param int $boundedFund
     * @return void
     */
    public function setBoundedFund(int $boundedFund): void
    {
        $this->boundedFund = $boundedFund;
    }

    /**
     * @param int $boundedFund
     * @return void
     */
    public function addBoundedFund(int $boundedFund): void
    {
        $this->boundedFund += $boundedFund;
    }

    /**
     * @return int
     */
    public function getUnboundedFund(): int
    {
        return $this->unboundedFund;
    }

    /**
     * @param int $unboundedFund
     * @return void
     */
    public function setUnboundedFund(int $unboundedFund): void
    {
        $this->unboundedFund = $unboundedFund;
    }

    /**
     * @param int $unboundedFund
     * @return void
     */
    public function addUnboundedFund(int $unboundedFund): void
    {
        $this->unboundedFund += $unboundedFund;
    }

    /**
     * @return int
     */
    public function getNuCoinFund(): int
    {
        return $this->nuCoinFund;
    }

    /**
     * @param int $nuCoinFund
     * @return void
     */
    public function setNuCoinFund(int $nuCoinFund): void
    {
        $this->nuCoinFund = $nuCoinFund;
    }

    /**
     * @param int $nuCoinFund
     * @return void
     */
    public function addNuCoinFund(int $nuCoinFund): void
    {
        $this->nuCoinFund += $nuCoinFund;
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
