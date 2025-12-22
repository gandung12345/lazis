<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use RuntimeException;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Dashboard extends AbstractEntity
{
    #[Json(name: 'donorCount')]
    private int $donorCount;

    #[Json(name: 'doneeCount')]
    private int $doneeCount;

    #[Json(name: 'volunteerCount')]
    private int $volunteerCount;

    #[Json(name: 'amilCount')]
    private int $amilCount;

    #[Json(name: 'organizerCount')]
    private int $organizerCount;

    #[Json(name: 'allWalletFunds')]
    private int $allWalletFunds;

    #[Json(name: 'nuCoinYearlyStatistics')]
    private array $nuCoinYearlyStatistics;

    #[Json(name: 'nuCoinAggregatorYearlyStatistics')]
    private array $nuCoinAggregatorYearlyStatistics;

    /**
     * @return int
     */
    public function getDonorCount(): int
    {
        return $this->donorCount;
    }

    /**
     * @param int $donorCount
     * @return void
     */
    public function setDonorCount(int $donorCount): void
    {
        $this->donorCount = $donorCount;
    }

    /**
     * @return int
     */
    public function getDoneeCount(): int
    {
        return $this->doneeCount;
    }

    /**
     * @param int $doneeCount
     * @return void
     */
    public function setDoneeCount(int $doneeCount): void
    {
        $this->doneeCount = $doneeCount;
    }

    /**
     * @return int
     */
    public function getVolunteerCount(): int
    {
        return $this->volunteerCount;
    }

    /**
     * @param int $volunteerCount
     * @return void
     */
    public function setVolunteerCount(int $volunteerCount): void
    {
        $this->volunteerCount = $volunteerCount;
    }

    /**
     * @return int
     */
    public function getAmilCount(): int
    {
        return $this->amilCount;
    }

    /**
     * @param int $amilCount
     * @return void
     */
    public function setAmilCount(int $amilCount): void
    {
        $this->amilCount = $amilCount;
    }

    /**
     * @return int
     */
    public function getOrganizerCount(): int
    {
        return $this->organizerCount;
    }

    /**
     * @param int $organizerCount
     * @return void
     */
    public function setOrganizerCount(int $organizerCount): void
    {
        $this->organizerCount = $organizerCount;
    }

    /**
     * @return int
     */
    public function getAllWalletFunds(): int
    {
        return $this->allWalletFunds;
    }

    /**
     * @param int $allWalletFunds
     * @return void
     */
    public function setAllWalletFunds(int $allWalletFunds): void
    {
        $this->allWalletFunds = $allWalletFunds;
    }

    /**
     * @return array
     */
    public function getNuCoinYearlyStatistics(): array
    {
        return $this->nuCoinYearlyStatistics;
    }

    /**
     * @param array $nuCoinYearlyStatistics
     * @return void
     */
    public function setNuCoinYearlyStatistics(array $nuCoinYearlyStatistics): void
    {
        $this->nuCoinYearlyStatistics = $nuCoinYearlyStatistics;
    }

    /**
     * @param \Schnell\Entity\EntityInterface $entity
     * @return void
     */
    public function addNuCoinYearlyStatistics(EntityInterface $entity): void
    {
        $this->nuCoinYearlyStatistics[] = $entity;
    }

    /**
     * @return array
     */
    public function getNuCoinAggregatorYearlyStatistics(): array
    {
        return $this->nuCoinAggregatorYearlyStatistics;
    }

    /**
     * @param array $nuCoinAggregatorYearlyStatistics
     * @return void
     */
    public function setNuCoinAggregatorYearlyStatistics(array $nuCoinAggregatorYearlyStatistics): void
    {
        $this->nuCoinAggregatorYearlyStatistics = $nuCoinAggregatorYearlyStatistics;
    }

    /**
     * @param \Schnell\Entity\EntityInterface $entity
     * @return void
     */
    public function addNuCoinAggregatorYearlyStatistics(EntityInterface $entity): void
    {
        $this->nuCoinAggregatorYearlyStatistics[] = $entity;
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
