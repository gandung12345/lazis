<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk;

use GuzzleHttp\ClientInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
abstract class AbstractSdk implements SdkInterface
{
    private ClientInterface $httpClient;

    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    public function setHttpClient(ClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }
}
