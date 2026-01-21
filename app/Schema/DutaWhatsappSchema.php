<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Rule;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DutaWhatsappSchema extends AbstractSchema
{
    #[Rule(required: true)]
    #[Json(name: 'url')]
    private ?string $url;

    #[Rule(required: true)]
    #[Json(name: 'path')]
    private ?string $path;

    #[Rule(required: true)]
    #[Json(name: 'apiKey')]
    private ?string $apiKey;

    public function __construct(
        ?string $url = null,
        ?string $path = null,
        ?string $apiKey = null
    ) {
        $this->setUrl($url);
        $this->setPath($path);
        $this->setApiKey($apiKey);
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }
}
