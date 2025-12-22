<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Entity\GeneratedError;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'users')]
#[OpenApi\Schema]
class Users extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Users ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'role')]
    #[OpenApi\Property(
        property: 'role',
        type: 'integer',
        description: 'Users role'
    )]
    // picklist: Role
    private int $role;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'password')]
    #[OpenApi\Property(
        property: 'password',
        type: 'string',
        description: 'Users password'
    )]
    private string $password;

    #[OneToOne(targetEntity: Organizer::class, inversedBy: 'users')]
    #[JoinColumn(name: 'organizerRefId', referencedColumnName: 'id')]
    private Organizer $organizer;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return void
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

    /**
     * @param int $role
     * @return void
     */
    public function setRole(int $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return Lazis\Api\Entity\Organizer
     */
    public function getOrganizer(): Organizer
    {
        return $this->organizer;
    }

    /**
     * @param Lazis\Api\Entity\Organizer $organizer
     * @return void
     */
    public function setOrganizer(Organizer $organizer): void
    {
        $this->organizer = $organizer;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__users__';
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'users';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return Users::class;
    }
}
