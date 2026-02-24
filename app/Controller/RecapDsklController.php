<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Throwable;
use Lazis\Api\Repository\RecapDsklRepository;
use Lazis\Api\Type\Role as RoleType;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Auth\Auth;
use Schnell\Attribute\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class RecapDsklController extends BaseController
{
    use ControllerTrait;

    #[Auth(
        role: [
            RoleType::ROOT, RoleType::ADMIN,
            RoleType::ADMIN_MASTER_DATA, RoleType::AGGREGATOR_ADMIN,
            RoleType::TASHARUF_ADMIN
        ]
    )]
    #[Route('/recap/{oid}/year/{year}/dskl', method: 'GET')]
    #[OpenApi\Get(
        path: '/recap/{oid}/year/{year}/dskl',
        tags: ['Recap'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getDsklRecap(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new RecapDsklRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $result = $repository->getRecap($args['oid'], $args['year']);
        } catch (Throwable $e) {
            return $this->handleRepositoryException($e, $response);
        }

        return $this->json($response, $result);
    }
}
