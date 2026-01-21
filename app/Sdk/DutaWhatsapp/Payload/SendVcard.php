<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk\DutaWhatsapp\Payload;

use Lazis\Api\Sdk\PayloadInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class SendVcard implements PayloadInterface
{
    private string $apiKey;

    private string $sender;

    private string $number;

    private string $name;

    private string $phone;

    public function __construct(
        string $apiKey,
        string $sender,
        string $number,
        string $name,
        string $phone
    ) {
        $this->setApiKey($apiKey);
        $this->setSender($sender);
        $this->setNumber($number);
        $this->setName($name);
        $this->setPhone($phone);
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phoneo = $phone;
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
            'name' => $this->getName(),
            'phone' => $this->getPhone()
        ];
    }
}
