<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use RuntimeException;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinAggregatorStatistics extends AbstractEntity
{
    #[Json(name: 'date')]
    private string $date;

    #[Json(name: 'incomingTransactionCount')]
    private int $incomingTransactionCount = 0;

    #[Json(name: 'outgoingTransactionCount')]
    private int $outgoingTransactionCount = 0;

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getIncomingTransactionCount(): int
    {
        return $this->incomingTransactionCount;
    }

    public function setIncomingTransactionCount(int $incomingTransactionCount): void
    {
        $this->incomingTransactionCount = $incomingTransactionCount;
    }

    public function incrementIncomingTransactionCount(): void
    {
        $this->incomingTransactionCount++;
    }

    public function getOutgoingTransactionCount(): int
    {
        return $this->outgoingTransactionCount;
    }

    public function setOutgoingTransactionCount(int $outgoingTransactionCount): void
    {
        $this->outgoingTransactionCount = $outgoingTransactionCount;
    }

    public function incrementOutgoingTransactionCount(): void
    {
        $this->outgoingTransactionCount++;
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
