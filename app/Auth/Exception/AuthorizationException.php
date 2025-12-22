<?php

declare(strict_types=1);

namespace Lazis\Api\Auth\Exception;

use Slim\Exception\HttpUnauthorizedException;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class AuthorizationException extends HttpUnauthorizedException
{
}
