<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Enum;
use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Rule;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class UsersSchema extends AbstractSchema
{
    use SchemaTrait;

    /**
     * @var int|null
     */
    #[Rule(required: true)]
    #[Json(name: 'role')]
    #[Enum(value: self::AUTH_ROLE_LIST)]
    private ?int $role;

    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'password')]
    private ?string $password;

    /**
     * @param int|null $role
     * @param string|null $password
     * @return static
     */
    public function __construct(?int $role = null, ?string $password = null)
    {
        $this->setRole($role);
        $this->setPassword($password);
    }

    /**
     * @return int|null
     */
    public function getRole(): ?int
    {
        return $this->role;
    }

    /**
     * @param int|null $role
     * @return void
     */
    public function setRole(?int $role): void
    {
        $this->role = $role;
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
