<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk\DutaWhatsapp;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Lazis\Api\Sdk\AbstractSdk;
use Lazis\Api\Sdk\PayloadInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DutaWhatsapp extends AbstractSdk
{
    private string $url;

    public function __construct(string $url)
    {
        $this->setUrl($url);
        $this->initializeHttpClient();
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    private function initializeHttpClient(): void
    {
        $this->setHttpClient(new Client(['base_uri' => $this->getUrl()]));
    }

    /**
     * @param \Lazis\Api\Sdk\PayloadInterface $payload
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sendMessage(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/send-message',
            ['json' => $payload->export()]
        );
    }

    public function sendMedia(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/send-media',
            ['json' => $payload->export()]
        );
    }

    public function sendPollMessage(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/send-poll',
            ['json' => $payload->export()]
        );
    }

    public function sendSticker(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/send-sticker',
            ['json' => $payload->export()]
        );
    }

    public function sendButton(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/send-button',
            ['json' => $payload->export()]
        );
    }

    public function sendListMessage(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/send-LIST',
            ['json' => $payload->export()]
        );
    }

    public function sendLocation(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/send-location',
            ['json' => $payload->export()]
        );
    }

    public function sendVcard(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/send-vcard',
            ['json' => $payload->export()]
        );
    }

    public function generateQrCode(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/generate-qr',
            ['json' => $payload->export()]
        );
    }

    public function disconnectDevice(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/logout-device',
            ['json' => $payload->export()]
        );
    }

    public function createUser(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/create-user',
            ['json' => $payload->export()]
        );
    }

    public function userInfo(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/info-user',
            ['json' => $payload->export()]
        );
    }

    public function deviceInfo(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/info-device',
            ['json' => $payload->export()]
        );
    }

    public function createDevice(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/create-device',
            ['json' => $payload->export()]
        );
    }

    public function checkNumber(PayloadInterface $payload): ResponseInterface
    {
        return $this->getHttpClient()->request(
            'POST',
            '/check-number',
            ['json' => $payload->export()]
        );
    }
}
