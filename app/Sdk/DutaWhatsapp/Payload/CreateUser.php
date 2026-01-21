<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk\DutaWhatsapp\Payload;

use Lazis\Api\Sdk\PayloadInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class CreateUser implements PayloadInterface
{
    private string $apiKey;

    private string $username;

    private string $password;

    private string $email;

    private int $expireAt;

    private int $limitDevice;

    public function __construct(
        string $apiKey,
        string $username,
        string $password,
        string $email,
        int $expireAt,
        int $limitDevice
    ) {
        $this->setApiKey($apiKey);
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setEmail($email);
        $this->setExpireAt($expireAt);
        $this->setLimitDevice($limitDevice);
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getExpireAt(): int
    {
        return $this->expireAt;
    }

    public function setExpireAt(int $expireAt): void
    {
        $this->expireAt = $expireAt;
    }

    public function getLimitDevice(): int
    {
        return $this->limitDevice;
    }

    public function setLimitDevice(int $limitDevice): void
    {
        $this->limitDevice = $limitDevice;
    }

    /**
     * {@inheritDoc}
     */
    public function export(): array
    {
        return [
            'api_key' => $this->getApiKey(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'email' => $this->getEmail(),
            'expire' => $this->getExpireAt(),
            'limit_device' => $this->getLimitDevice()
        ];
    }
}
