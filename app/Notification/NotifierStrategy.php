<?php

declare(strict_types=1);

namespace Lazis\Api\Notification;

use Schnell\Config\ConfigInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NotifierStrategy implements NotifierStrategyInterface
{
    /**
     * @var \Schnell\Config\ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var \Lazis\Api\Notification\NotifierInterface
     */
    private NotifierInterface $notifier;

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
    public function getNotifier(): NotifierInterface
    {
        return $this->notifier;
    }

    /**
     * {@inheritDoc}
     */
    public function setNotifier(NotifierInterface $notifier): void
    {
        $this->notifier = $notifier;
    }

    /**
     * {@inheritDoc}
     */
    public function notify(PayloadInterface $payload): void
    {
        $this->getNotifier()->notify($payload);
    }
}
