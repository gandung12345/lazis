<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Throwable;
use Lazis\Api\Http\Response\Builder as ResponseBuilder;
use Lazis\Api\Repository\InfaqDistributionContextualRepository;
use Lazis\Api\Type\Role as RoleType;
use OpenApi\Attributes as OpenApi;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Schnell\Attribute\Auth\Auth;
use Schnell\Attribute\Route;
use Schnell\Http\Code as HttpCode;
use Schnell\Paginator\Paginator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class InfaqDistributionContextualController extends BaseController
{
    use ControllerTrait;

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[Auth(
        role: [
            RoleType::ROOT, RoleType::ADMIN,
            RoleType::ADMIN_MASTER_DATA, RoleType::AGGREGATOR_ADMIN,
            RoleType::TASHARUF_ADMIN
        ]
    )]
    #[Route('/organizationContext/{oid}/infaqDistribution', method: 'GET')]
    #[OpenApi\Get(
        path: '/organizationContext/{oid}/infaqDistribution',
        tags: ['Infaq Distribution Context'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 400, description: 'Bad Request')
        ]
    )]
    public function getAllInfaqDistributions(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new InfaqDistributionContextualRepository(
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
