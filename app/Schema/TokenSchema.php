<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Rule;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class TokenSchema extends AbstractSchema
{
    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'email')]
    private ?string $email;

    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'password')]
    private ?string $password;

    /**
     * @param string|null $email
     * @param string|null $password
     * @return static
     */
    public function __construct(?string $email = null, ?string $password = null)
    {
        $this->setEmail($email);
        $this->setPassword($password);
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return void
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return void
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }
}
