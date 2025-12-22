<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use RuntimeException;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinAggregatorCrossWalletSuccessResponse extends AbstractEntity
{
    #[Json(name: 'code')]
    #[OpenApi\Property(
        property: 'code',
        type: 'integer',
        description: 'NU coin aggregator cross wallet success response code'
    )]
    private int $code;

    #[Json(name: 'organizationId')]
    #[OpenApi\Property(
        property: 'organizationId',
        type: 'string',
        description: 'NU coin aggregator cross wallet success response organization ID'
    )]
    private string $organizationId;

    #[Json(name: 'transferredAmount')]
    #[OpenApi\Property(
        property: 'transferredAmount',
        type: 'integer',
        description: 'NU coin aggregator cross wallet success response transferred amount'
    )]
    private int $transferredAmount;

    public function __construct(int $code, string $organizationId, int $transferredAmount)
    {
        $this->setCode($code);
        $this->setOrganizationId($organizationId);
        $this->setTransferredAmount($transferredAmount);
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }

    public function setOrganizationId(string $organizationId): void
    {
        $this->organizationId = $organizationId;
    }

    public function getTransferredAmount(): int
    {
        return $this->transferredAmount;
    }

    public function setTransferredAmount(int $transferredAmount): void
    {
        $this->transferredAmount = $transferredAmount;
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
