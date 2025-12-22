<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use OpenApi\Attributes as OpenApi;
use Schnell\Controller\AbstractController;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[OpenApi\Info(
    title: "LazisNU REST API",
    version: "0.1-dev"
)]
class BaseController extends AbstractController
{
}
