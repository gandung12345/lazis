<?php

declare(strict_types=1);

namespace Lazis\Api\Notification;

use Lazis\Api\Sdk\SdkInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface NotifierInterface
{
    /**
     * @return \Lazis\Api\Sdk\SdkInterface
     */
    public function getSdk(): SdkInterface;

    /**
     * @param \Lazis\Api\Sdk\SdkInterface $sdk
     * @return void
     */
    public function setSdk(SdkInterface $sdk): void;
}
