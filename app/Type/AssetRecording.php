<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class AssetRecording
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int CURRENT_ASSET = 1;

    /**
     * @var int
     */
    public const int NON_CURRENT_ASSET = 2;
}
