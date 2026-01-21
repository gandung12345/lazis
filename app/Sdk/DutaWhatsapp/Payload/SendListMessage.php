<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk\DutaWhatsapp\Payload;

use Lazis\Api\Sdk\PayloadInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class SendListMessage implements PayloadInterface
{
    private string $apiKey;

    private string $sender;

    private string $number;

    private string $footer;

    private string $title;

    private string $buttonText;

    private string $message;

    private array $list;

    private string $url;

    public function __construct(
        string $apiKey,
        string $sender,
        string $number,
        string $footer,
        string $title,
        string $buttonText,
        string $message,
        array $list,
        string $url
    ) {
        $this->setApiKey($apiKey);
        $this->setSender($sender);
        $this->setNumber($number);
        $this->setFooter($footer);
        $this->setTitle($title);
        $this->setButtonText($buttonText);
        $this->setMessage($message);
        $this->setList($list);
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

    public function getFooter(): string
    {
        return $this->footer;
    }

    public function setFooter(string $footer): void
    {
        $this->footer = $footer;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getButtonText(): string
    {
        return $this->buttonText;
    }

    public function setButtonText(string $buttonText): void
    {
        $this->buttonText = $buttonText;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function setList(array $list): void
    {
        $this->list = $list;
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
            'footer' => $this->getFooter(),
            'title' => $this->getTitle(),
            'buttontext' => $this->getButtonText(),
            'message' => $this->getMessage(),
            'list' => $this->getList(),
            'url' => $this->getUrl()
        ];
    }
}
