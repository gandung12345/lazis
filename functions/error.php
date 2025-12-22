<?php

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */

declare(strict_types=1);

use Psr\Http\Message\RequestInterface;
use Slim\Psr7\Response;
use Slim\ResponseEmitter;

/**
 * @param Psr\Http\Request\RequestInterface $request
 * @return void
 */
function shutdownHandler(RequestInterface $request): void
{
    $error = error_get_last();

    if (!$error) {
        return;
    }

    $serverParams = $request->getServerParams();
    $response = (new Response())
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withHeader(
            'Access-Control-Allow-Origin',
            $serverParams['HTTP_ORIGIN'] ?? ''
        )
        ->withHeader(
            'Access-Control-Allow-Headers',
            'X-Requested-With, Content-Type, Accept, Origin, Authorization'
        )
        ->withHeader(
            'Access-Control-Allow-Methods',
            'GET, POST, PUT, PATCH, DELETE, OPTIONS'
        )
        ->withHeader(
            'Cache-Control',
            'no-store, no-cache, must-revalidate, max-age=0'
        )
        ->withAddedHeader('Cache-Control', 'post-check=0, pre-check=0')
        ->withHeader('Pragma', 'no-cache');
    $emitter = new ResponseEmitter();

    if (ob_get_contents()) {
        ob_clean();
    }

    $errorObj = [
        'file' => $error['file'],
        'line' => $error['line'],
        'message' => $error['message'],
        'type' => $error['type']
    ];

    $response->getBody()
        ->write(json_encode($errorObj));

    $emitter->emit($response->withHeader('Content-Type', 'application/json'));
}

/**
 * @param Psr\Http\Message\RequestInterface $request
 * @return callable
 */
function shutdownHandlerCallback(RequestInterface $request): callable
{
    return function ($request) {
        shutdownHandler($request);
    };
}

/**
 * @param callable $shutdownHandler
 * @param Psr\Http\Message\RequestInterface $request
 * @return void
 */
function registerShutdownHandler(
    RequestInterface $request,
    callable $shutdownHandler
): void {
    register_shutdown_function($shutdownHandler, $request);
}
