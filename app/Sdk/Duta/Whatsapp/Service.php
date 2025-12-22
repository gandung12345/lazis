<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk\Duta\Whatsapp;

use GuzzleHttp\Client;
use Lazis\Api\Sdk\ServiceInterface as GenericServiceInterface;
use Schnell\Config\ConfigInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Service implements GenericServiceInterface
{
    /**
     * @var \Schnell\Config\ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @param \Schnell\Config\ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
    public function setConfig(ConfigInterface $config): void
    {
        $this->config = $config;
    }

    /**
     * @param \Lazis\Api\Sdk\Duta\Whatsapp\Payload $payload
     * @return array
     */
    public function send(Payload $payload): array
    {
        $client = new Client([
            'base_uri' => $this->getConfig()->get('duta-whatsapp.url')
        ]);

        return $client->post('/', [
            'json' => [
                'api_key' => $this->getConfig()->get('duta-whatsapp.apiKey'),
                'sender' => $payload->getSender(),
                'number' => $payload->getReceiver(),
                'message' => $payload->getContent()
            ]
        ]);
    }
}
