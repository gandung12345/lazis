<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk;

use Lazis\Api\Sdk\ServiceInterface;
use Lazis\Api\Sdk\Duta\Whatsapp\Service as DutaWhatsappService;
use Schnell\Config\ConfigInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class ServiceFactory implements ServiceFactoryInterface
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
     * {@inheritDoc}
     */
    public function createDutaWhatsapp(): ServiceInterface
    {
        return new DutaWhatsappService($this->getConfig());
    }
}
