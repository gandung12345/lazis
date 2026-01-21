<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk\DutaWhatsapp\Payload;

use Lazis\Api\Sdk\PayloadInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class SendMedia implements PayloadInterface
{
    private string $apiKey;

    private string $sender;

    private string $number;

    private string $mediaType;

    private string $caption;

    private string $url;

    public function __construct(
        string $apiKey,
        string $sender,
        string $number,
        string $mediaType,
        string $caption,
        string $url
    ) {
        $this->setApiKey($apiKey);
        $this->setSender($sender);
        $this->setNumber($number);
        $this->setMediaType($mediaType);
        $this->setCaption($caption);
        $this->setUrl($url);
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function setSender(string $sender): void
    {
        $this->sender = $sender;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    public function setMediaType(string $mediaType): void
    {
        $this->mediaType = $mediaType;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): void
    {
        $this->caption = $caption;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * {@inheritDoc}
     */
    public function export(): array
    {
        return [
            'api_key' => $this->getApiKey(),
            'sender' => $this->getSender(),
            'number' => $this->getNumber(),
            'media_type' => $this->getMediaType(),
            'caption' => $this->getCaption(),
            'url' => $this->getUrl()
        ];
    }
}
