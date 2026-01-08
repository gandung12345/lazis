<?php

declare(strict_types=1);

namespace Lazis\Api\Notification\Payload;

use Lazis\Api\Notification\PayloadInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DutaWhatsappPayload implements PayloadInterface
{
    private string $apiKey;

    private string $sender;

    private string $receiver;

    private string $message;

    public function __construct(
        string $apiKey,
        string $sender,
        string $receiver,
        string $message
    ) {
        $this->setApiKey($apiKey);
        $this->setSender($sender);
        $this->setReceiver($receiver);
        $this->setMessage($message);
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function setSender(string $sender): void
    {
        $this->sender = $sender;
    }

    public function getReceiver(): string
    {
        return $this->receiver;
    }

    public function setReceiver(string $receiver): void
    {
        $this->receiver = $receiver;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function serialize(): array
    {
        return [
            'api_key' => $this->getApiKey(),
            'sender' => $this->getSender(),
            'number' => $this->getReceiver(),
            'message' => $this->getMessage()
        ];
    }
}
