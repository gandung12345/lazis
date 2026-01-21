<?php

declare(strict_types=1);

namespace Lazis\Api\Notification;

use Lazis\Api\Sdk\PayloadInterface as SdkPayloadInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface PayloadInterface
{
    public function export(): SdkPayloadInterface;
}
