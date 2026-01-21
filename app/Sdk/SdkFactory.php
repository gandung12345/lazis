<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk;

use Lazis\Api\Sdk\DutaWhatsapp\DutaWhatsapp;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class SdkFactory implements SdkFactoryInterface
{
    /**
     * @return \Lazis\Api\Sdk\SdkFactoryInterface
     */
    public static function create(): SdkFactoryInterface
    {
        return new static();
    }

    /**
     * @param string $url
     * @return \Lazis\Api\Sdk\SdkInterface
     */
    public function getDutaWhatsapp(string $url): SdkInterface
    {
        return new DutaWhatsapp($url);
    }
}
