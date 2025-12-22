<?php

declare(strict_types=1);

namespace Schnell\Tests\Bridge;

use Schnell\Bridge\AbstractBridge;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ConcreteBridge extends AbstractBridge
{
    /**
     * {@inheritdoc}
     */
    public function load(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'concrete-bridge';
    }
}
