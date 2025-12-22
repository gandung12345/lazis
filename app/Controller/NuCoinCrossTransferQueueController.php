<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Lazis\Api\Entity\NuCoinCrossTransferQueue;
use Lazis\Api\Http\Response\Builder as ResponseBuilder;
use Lazis\Api\Repository\NuCoinCrossTransferQueueRepository;
use Lazis\Api\Schema\NuCoinCrossTransferQueueSchema;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Route;
use Schnell\Http\Code as HttpCode;
use Schnell\Paginator\Paginator;
use Schnell\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinCrossTransferQueueController extends BaseController
{
    #[Route('/nuCoinCrossTransferQueue', method: 'GET')]
    #[OpenApi\Get(
        path: '/nuCoinCrossTransferQueue',
        tags: ['NU Coin Cross Transfer Queue'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK')
        ]
    )]
    public function getAllNuCoinCrossTransferQueues(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new NuCoinCrossTransferQueueRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $count = $this->getContainer()
            ->get('mapper')
            ->withRequest($request)
            ->count(new NuCoinCrossTransferQueue());

        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($request);

        return $this->hateoas(
            $request,
            $response,
            $page,
            $repository->paginate()
        );
    }

    #[Route('/nuCoinCrossTransferQueue', method: 'POST')]
    #[OpenApi\Post(
        path: '/nuCoinCrossTransferQueue',
        tags: ['NU Coin Cross Transfer Queue'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created')
        ]
    )]
    public function createNuCoinCrossTransferQueue(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new NuCoinCrossTransferQueueSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new NuCoinCrossTransferQueueRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        return $this->json(
            $response,
            $repository->create($schema),
            HttpCode::CREATED
        );
    }

    #[Route('/nuCoinCrossTransferQueue/{id}', method: 'GET')]
    #[OpenApi\Get(
        path: '/nuCoinCrossTransferQueue/{id}',
        tags: ['NU Coin Cross Transfer Queue'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getNuCoinCrossTransferQueueById(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new NuCoinCrossTransferQueueRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $result = $repository->getById($args['id'], true);
        } catch (Throwable $e) {
            $result = null;
        }

        if (null === $result) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'NU coin cross transfer queue data with id \'%s\' not found.',
                    $args['id']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $result);
    }

    #[Route('/nuCoinCrossTransferQueue/{id}', method: 'PUT')]
    #[OpenApi\Put(
        path: '/nuCoinCrossTransferQueue/{id}',
        tags: ['NU Coin Cross Transfer Queue'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function updateNuCoinCrossTransferQueue(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new NuCoinCrossTransferQueueSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assignOptional($schema);

        $repository = new NuCoinCrossTransferQueueRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->update($args['id'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'NU coin cross transfer queue data with id \'%s\' not found.',
                    $args['id']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity);
    }
}
