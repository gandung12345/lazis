<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk;

use GuzzleHttp\ClientInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface SdkInterface
{
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
