<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk\DutaWhatsapp\Payload;

use Lazis\Api\Sdk\PayloadInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class SendButton implements PayloadInterface
{
    private string $apiKey;

    private string $sender;

    private string $number;

    private string $message;

    private array $button;

    private string $footer;

    private string $url;

    public function __construct(
        string $apiKey,
        string $sender,
        string $number,
        string $message,
        array $button,
        string $footer,
        string $url
    ) {
        $this->setApiKey($apiKey);
        $this->setSender($sender);
        $this->setNumber($number);
        $this->setMessage($message);
        $this->setButton($button);
        $this->setFooter($footer);
        $this->setUrl($url);
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

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getButton(): array
    {
        return $this->button;
    }

    public function setButton(array $button): void
    {
        $this->button = $button;
    }

    public function getFooter(): string
    {
        return $this->footer;
    }

    public function setFooter(string $footer): void
    {
        $this->footer = $footer;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * {@inheritDoc}
     */
    public function export(): array
    {
        return [
            'api_key' => $this->getApiKey(),
            'sender' => $this->getSender(),
            'number' => $this->getNumber(),
            'message' => $this->getMessage(),
            'button' => $this->getButton(),
            'footer' => $this->getFooter(),
            'url' => $this->getUrl()
        ];
    }
}
