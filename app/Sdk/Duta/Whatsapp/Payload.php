<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk\Duta\Whatsapp;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Payload
{
    /**
     * @var string|null
     */
    private ?string $sender;

    /**
     * @var string|null
     */
    private ?string $receiver;

    /**
     * @var string|null
     */
    private ?string $content;

    /**
     * @param string|null $sender
     * @param string|null $receiver
     * @param string|null $content
     */
    public function __construct(
        ?string $sender = null,
        ?string $receiver = null,
        ?string $content = null
    ) {
        $this->setSender($sender);
        $this->setReceiver($receiver);
        $this->setContent($content);
    }

    /**
     * @return string|null
     */
    public function getSender(): ?string
    {
        return $this->sender;
    }

    /**
     * @param string|null $sender
     * @return void
     */
    public function setSender(?string $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return string|null
     */
    public function getReceiver(): ?string
    {
        return $this->receiver;
    }

    /**
     * @param string|null $receiver
     * @return void
     */
    public function setReceiver(?string $receiver): void
    {
        $this->receiver = $receiver;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return void
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }
}
