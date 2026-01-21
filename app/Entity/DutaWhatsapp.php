<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'dutaWhatsapp')]
#[OpenApi\Schema]
class DutaWhatsapp extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Duta whatsapp ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'url')]
    #[OpenApi\Property(
        property: 'url',
        type: 'string',
        description: 'Duta whatsapp url'
    )]
    private string $url;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'path')]
    #[OpenApi\Property(
        property: 'path',
        type: 'string',
        description: 'Duta whatsapp path'
    )]
    private string $path;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'apiKey')]
    #[OpenApi\Property(
        property: 'apiKey',
        type: 'string',
        description: 'Duta whatsapp api key'
    )]
    private string $apiKey;

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
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return void
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return void
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return void
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__dutaWhatsapp__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'dutaWhatsapp';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return DutaWhatsapp::class;
    }
}
