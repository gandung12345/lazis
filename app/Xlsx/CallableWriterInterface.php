<?php

declare(strict_types=1);

namespace Lazis\Api\Xlsx;

use Psr\Http\Message\ResponseInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface CallableWriterInterface
{
    /**
     * @param mixed ...$args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(mixed ...$args): ResponseInterface;
}
