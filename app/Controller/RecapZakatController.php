<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Throwable;
use Lazis\Api\Repository\RecapZakatRepository;
use Lazis\Api\Type\Role as RoleType;
use OpenApi\Attributes;
use Schnell\Attribute\Auth\Auth;
use Schnell\Attribute\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class RecapZakatController extends BaseController
{
    #[Auth(
        role: [
            RoleType::ROOT, RoleType::ADMIN,
            RoleType::ADMIN_MASTER_DATA, RoleType::AGGREGATOR_ADMIN,
            RoleType::TASHARUF_ADMIN
        ]
    )]
    #[Route('/recap/{oid}/year/{year}/zakat', method: 'GET')]
    #[OpenApi\Get(
        path: '/recap/{oid}/year/{year}/zakat',
        tags: ['Recap'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getZakatRecap(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new RecapZakatRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $result = $repository->getRecap($args['oid'], $args['year']);
        } catch (Throwable $e) {
            $result = [];
        }

        return $this->json($response, $result);
    }
}
