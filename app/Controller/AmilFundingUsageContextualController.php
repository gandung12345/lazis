<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Throwable;
use Lazis\Api\Http\Response\Builder as ResponseBuilder;
use Lazis\Api\Repository\AmilFundingUsageContextualRepository;
use OpenApi\Attributes as OpenApi;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Schnell\Attribute\Route;
use Schnell\Http\Code as HttpCode;
use Schnell\Paginator\Paginator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class AmilFundingUsageContextualController extends BaseController
{
    use ControllerTrait;

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[Route('/organizationContext/{oid}/amilFundingUsage', method: 'GET')]
    #[OpenApi\Get(
        path: '/organizationContext/{oid}/amilFundingUsage',
        tags: ['Amil Funding Usage Context'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 400, description: 'Bad Request')
        ]
    )]
    public function getAllAmilFundingUsages(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new AmilFundingUsageContextualRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $count = $repository->count($args['oid']);
            $paginator = new Paginator($count);
            $page = $paginator->getMetadata($request);
            $result = $repository->paginate($args['oid']);
        } catch (Throwable $e) {
            return $this->handleRepositoryException($e, $response);
        }

        return $this->hateoas($request, $response, $page, $result);
    }
}
