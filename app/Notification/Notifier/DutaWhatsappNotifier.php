<?php

declare(strict_types=1);

namespace Lazis\Api\Notification\Notifier;

use GuzzleHttp\Client;
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
    public function initializeHttpClient(): void
    {
        $this->setHttpClient(new Client(['base_uri' => $this->getConfig()->get('duta-whatsapp.url')]));
    }

    /**
     * {@inheritDoc}
     */
    public function notify(PayloadInterface $payload): void
    {
        $response = $this->getHttpClient()->request(
            'POST',
            $this->getConfig()->get('duta-whatsapp.path'),
            ['json' => $payload->serialize()]
        );
    }
}
