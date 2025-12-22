<?php

declare(strict_types=1);

namespace Schnell\Middleware;

use Psr\Http\Message\ResponseInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
trait MiddlewareTrait
{
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function injectCorsHeader(ResponseInterface $response): ResponseInterface
    {
        return $response
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Content-Type', 'application/json');
    }
}
