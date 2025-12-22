<?php

declare(strict_types=1);

namespace Lazis\Api\Notification;

use Schnell\Config\ConfigInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface NotifierStrategyInterface
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
     * @return \Lazis\Api\Notification\NotifierInterface
     */
    public function getNotifier(): NotifierInterface;

    /**
     * @param \Lazis\Api\Notification\NotifierInterface $notifier
     * @return void
     */
    public function setNotifier(NotifierInterface $notifier): void;

    /**
     * @param \Lazis\Api\Notification\PayloadInterface $payload
     * @return void
     */
    public function notify(PayloadInterface $payload): void;
}
