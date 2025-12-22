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
class NuCoinAggregatorCrossWalletFaultResponse extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'code')]
    #[OpenApi\Property(
        property: 'code',
        type: 'integer',
        description: 'NU coin aggregator cross wallet fault response code'
    )]
    private int $code;

    #[Json(name: 'message')]
    #[OpenApi\Property(
        property: 'message',
        type: 'string',
        description: 'NU coin aggregator cross wallet fault response message'
    )]
    private string $message;

    public function __construct(int $code, string $message)
    {
        $this->setCode($code);
        $this->setMessage($message);
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
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
