<?php

declare(strict_types=1);

namespace Lazis\Api\Notification;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NotifierStrategy implements NotifierStrategyInterface
{
    /**
     * @var \Lazis\Api\Notification\NotifierInterface
     */
    private NotifierInterface $notifier;

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
