<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'assetRecording')]
#[OpenApi\Schema]
class AssetRecording extends AbstractEntity
{
    /**
     * @var string
     */
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Asset recording ID',
        readOnly: true
    )]
    private string $id;

    /**
     * @var int
     */
    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'kind')]
    #[OpenApi\Property(
        property: 'kind',
        type: 'integer',
        description: 'Asset recording kind'
    )]
    private int $kind;

    /**
     * @var string
     */
    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'name')]
    #[OpenApi\Property(
        property: 'name',
        type: 'string',
        description: 'Asset recording name'
    )]
    private string $name;

    /**
     * @var \Schnell\Decorator\Stringified\DateTimeDecorator
     */
    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'date')]
    #[OpenApi\Property(
        property: 'date',
        type: 'string',
        format: 'date',
        description: 'Asset recording date'
    )]
    private DateTimeDecorator $date;

    /**
     * @var int
     */
    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'price')]
    #[OpenApi\Property(
        property: 'price',
        type: 'integer',
        description: 'Asset recording price'
    )]
    private int $price;

    /**
     * @var \Lazis\Api\Entity\Organization
     */
    #[ManyToOne(targetEntity: Organization::class, inversedBy: 'assetRecordings')]
    #[JoinColumn(name: 'organizationRefId', referencedColumnName: 'id')]
    private Organization $organization;

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
    public function getKind(): int
    {
        return $this->kind;
    }

    /**
     * @param int $kind
     * @return void
     */
    public function setKind(int $kind): void
    {
        $this->kind = $kind;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \Schnell\Decorator\Stringified\DateTimeDecorator
     */
    public function getDate(): DateTimeDecorator
    {
        return $this->date;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator $date
     * @return void
     */
    public function setDate(DateTimeDecorator $date): void
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     * @return void
     */
    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    /**
     * @return \Lazis\Api\Entity\Organization
     */
    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    /**
     * @param \Lazis\Api\Entity\Organization $organization
     * @return void
     */
    public function setOrganization(Organization $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return AssetRecording::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__assetRecording__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'assetRecording';
    }
}
