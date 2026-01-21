<?php

declare(strict_types=1);

namespace Lazis\Api\Notification\Notifier;

use Lazis\Api\Notification\AbstractNotifier;
use Lazis\Api\Notification\PayloadInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DutaWhatsappNotifier extends AbstractNotifier
{
    /**
     * {@inheritDoc}
     */
    public function notify(PayloadInterface $payload): void
    {
        $response = $this
            ->getSdk()
            ->sendMessage($payload->export());
    }
}
