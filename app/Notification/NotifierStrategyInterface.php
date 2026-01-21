<?php

declare(strict_types=1);

namespace Lazis\Api\Notification;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface NotifierStrategyInterface
{
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
