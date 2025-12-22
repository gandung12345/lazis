<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Throwable;
use Lazis\Api\Http\Response\Builder as ResponseBuilder;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Schnell\Http\Code as HttpCode;
use Schnell\Schema\SchemaInterface;
use Schnell\Validator\Validator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
trait ControllerTrait
{
    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     */
    private function transformRequestToSchemaList(
        RequestInterface $request,
        SchemaInterface $schema
    ): array {
        return Validator::create($request)->assignMultiple($schema);
    }

    /**
     * @param \Throwable $throwable
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param int $httpCode
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleRepositoryException(
        Throwable $e,
        ResponseInterface $response,
        int $httpCode = HttpCode::BAD_REQUEST
    ): ResponseInterface {
        $builder = new ResponseBuilder();
        $builder = $builder
            ->withPair('code', $httpCode)
            ->withPair('message', $e->getMessage());

        return $this->json($response, $builder->build(), $httpCode);
    }
}
