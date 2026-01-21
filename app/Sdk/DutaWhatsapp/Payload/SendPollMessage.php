<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk\Payload;

use Lazis\Api\Sdk\PayloadInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class SendPollMessage implements PayloadInterface
{
    private string $apiKey;

    private string $sender;

    private string $number;

    private string $name;

    private array $option;

    private string $countable;

    public function __construct(
        string $apiKey,
        string $sender,
        string $number,
        string $name,
        array $option,
        string $countable
    ) {
        $this->setApiKey($apiKey);
        $this->setSender($sender);
        $this->setNumber($number);
        $this->setName($name);
        $this->setOption($option);
        $this->setCountable($countable);
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

    public function getOption(): array
    {
        return $this->option;
    }

    public function setOption(array $option): void
    {
        $this->option = $option;
    }

    public function getCountable(): string
    {
        return $this->countable;
    }

    public function setCountable(string $countable): void
    {
        $this->countable = $countable;
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
            'option' => $this->getOption(),
            'countable' => $this->getCountable()
        ];
    }
}
