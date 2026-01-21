<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface SdkFactoryInterface
{
    /**
     * @return \Lazis\Api\Sdk\SdkInterface
     */
    public function getDutaWhatsapp(string $url): SdkInterface;
}
