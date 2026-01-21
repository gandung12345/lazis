<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk\DutaWhatsapp\Payload;

use Lazis\Api\Sdk\PayloadInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class UserInfo implements PayloadInterface
{
    private string $apiKey;

    private string $username;

    public function __construct(string $apiKey, string $username)
    {
        $this->setApiKey($apiKey);
        $this->setUsername($username);
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * {@inheritDoc}
     */
    public function export(): array
    {
        return [
            'api_key' => $this->getApiKey(),
            'username' => $this->getUsername()
        ];
    }
}
