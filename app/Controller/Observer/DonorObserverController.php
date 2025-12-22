<?php

declare(strict_types=1);

namespace Lazis\Api\Controller\Observer;

use Lazis\Api\Controller\BaseController;
use Lazis\Api\Controller\ControllerTrait;
use Lazis\Api\Repository\Observer\DonorObserverRepository;
use OpenApi\Attributes as OpenApi;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Schnell\Attribute\Route;
use Schnell\Paginator\Paginator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DonorObserverController extends BaseController
{
    use ControllerTrait;

    #[Route(
        '/observer/donor/{scope}/{district}/{village}',
        method: 'GET'
    )]
    #[OpenApi\Get(
        path: '/observer/donor/{scope}/{district}/{village}',
        tags: ['Observer'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK')
        ]
    )]
    public function getAllDonors(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new DonorObserverRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $count = $repository->count(
            intval($args['scope']),
            $args['district'],
            $args['village']
        );

        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($request);

        $result = $repository->paginate(
            intval($args['scope']),
            $args['district'],
            $args['village']
        );

        return $this->hateoas($request, $response, $page, $result);
    }
}
