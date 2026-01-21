<?php

declare(strict_types=1);

namespace Lazis\Api\Notification;

use Lazis\Api\Sdk\SdkInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
abstract class AbstractNotifier implements NotifierInterface
{
    private SdkInterface $sdk;

    /**
     * @param \Lazis\Api\Sdk\SdkInterface $sdk
     */
    public function __construct(SdkInterface $sdk)
    {
        $this->setSdk($sdk);
    }

    public function getSdk(): SdkInterface
    {
        return $this->sdk;
    }

    public function setSdk(SdkInterface $sdk): void
    {
        $this->sdk = $sdk;
    }
}
