<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report;

use Lazis\Api\Entity\Report\Components\CurrentAsset;
use Lazis\Api\Entity\Report\Components\NonCurrentAsset;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class AssetRecordingReport extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'year')]
    private int $year;

    /**
     * @var array
     */
    #[Json(name: 'currentAssets')]
    private array $currentAssets;

    /**
     * @var array
     */
    #[Json(name: 'nonCurrentAssets')]
    private array $nonCurrentAssets;

    /**
     * @param int $year
     * @param array $currentAssets
     * @param array $nonCurrentAssets
     */
    public function __construct(
        int $year = 0,
        array $currentAssets = [],
        array $nonCurrentAssets = []
    ) {
        $this->setYear($year);
        $this->setCurrentAssets($currentAssets);
        $this->setNonCurrentAssets($nonCurrentAssets);
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
     * @return array
     */
    public function getCurrentAssets(): array
    {
        return $this->currentAssets;
    }

    /**
     * @param array $currentAssets
     * @return void
     */
    public function setCurrentAssets(array $currentAssets): void
    {
        $this->currentAssets = $currentAssets;
    }

    /**
     * @param \Lazis\Api\Entity\Report\Components\CurrentAsset $currentAsset
     * @return void
     */
    public function addCurrentAsset(CurrentAsset $currentAsset): void
    {
        $this->currentAssets[] = $currentAsset;
    }

    /**
     * @return array
     */
    public function getNonCurrentAssets(): array
    {
        return $this->nonCurrentAssets;
    }

    /**
     * @param array $nonCurrentAssets
     * @return void
     */
    public function setNonCurrentAssets(array $nonCurrentAssets): void
    {
        $this->nonCurrentAssets = $nonCurrentAssets;
    }

    /**
     * @param \Lazis\Api\Entity\Report\Components\NonCurrentAsset $nonCurrentAsset
     * @return void
     */
    public function addNonCurrentAsset(NonCurrentAsset $nonCurrentAsset): void
    {
        $this->nonCurrentAssets[] = $nonCurrentAsset;
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
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
    public function getQueryBuilderAlias(): string
    {
        throw new RuntimeException("Not implemented");
    }
}
