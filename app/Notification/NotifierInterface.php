<?php

declare(strict_types=1);

namespace Lazis\Api\Notification;

use GuzzleHttp\ClientInterface;
use Schnell\Config\ConfigInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface NotifierInterface
{
    /**
     * @return \Schnell\Config\ConfigInterface
     */
    public function getConfig(): ConfigInterface;

    /**
     * @param \Schnell\Config\ConfigInterface $config
     * @return void
     */
    public function setConfig(ConfigInterface $config): void;

    /**
     * @return \GuzzleHttp\ClientInterface
     */
    public function getHttpClient(): ClientInterface;

    /**
     * @param \GuzzleHttp\ClientInterface $httpClient
     * @return void
     */
    public function setHttpClient(ClientInterface $httpClient): void;
}
