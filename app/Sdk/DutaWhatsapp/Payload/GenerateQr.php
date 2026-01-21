<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk\DutaWhatsapp\Payload;

use Lazis\Api\Sdk\PayloadInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class GenerateQr implements PayloadInterface
{
    private string $apiKey;

    private string $device;

    private bool $force;

    public function __construct(
        string $apiKey,
        string $device,
        bool $force
    ) {
        $this->setApiKey($apiKey);
        $this->setDevice($device);
        $this->setForce($force);
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    public function setDevice(string $device): void
    {
        $this->device = $device;
    }

    public function getForce(): bool
    {
        return $this->force;
    }

    public function setForce(bool $force): void
    {
        $this->force = $force;
    }

    /**
     * {@inheritDoc}
     */
    public function export(): array
    {
        return [
            'api_key' => $this->getApiKey(),
            'device' => $this->getDevice(),
            'force' => $this->getForce()
        ];
    }
}
