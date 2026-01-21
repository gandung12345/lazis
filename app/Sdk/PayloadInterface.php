<?php

declare(strict_types=1);

namespace Lazis\Api\Sdk;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface PayloadInterface
{
    /**
     * @return array
     */
    public function export(): array;
}
