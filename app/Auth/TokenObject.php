<?php

declare(strict_types=1);

namespace Lazis\Api\Auth;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class TokenObject
{
    /**
     * @var string|null
     */
    private ?string $token;

    /**
     * @var int|null
     */
    private ?int $lifetime;

    /**
     * @param string|null $token
     * @param int|null $lifetime
     * @return static
     */
    public function __construct(?string $token = null, ?int $lifetime = null)
    {
        $this->setToken($token);
        $this->setLifetime($lifetime);
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return void
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return int|null
     */
    public function getLifetime(): ?int
    {
        return $this->lifetime;
    }

    /**
     * @param int|null $lifetime
     * @return void
     */
    public function setLifetime(?int $lifetime): void
    {
        $this->lifetime = $lifetime;
    }
}
