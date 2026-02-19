<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Throwable;
use Lazis\Api\Csv\Processor;
use Lazis\Api\Entity\NuCoin;
use Lazis\Api\Http\Response\Builder as ResponseBuilder;
use Lazis\Api\Repository\NuCoinRepository;
use Lazis\Api\Schema\NuCoinBulkSchema;
use Lazis\Api\Schema\NuCoinSchema;
use Lazis\Api\Type\Role as RoleType;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Auth\Auth;
use Schnell\Attribute\Route;
use Schnell\Http\Code as HttpCode;
use Schnell\Paginator\Paginator;
use Schnell\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinController extends BaseController
{
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
    #[Route('/nuCoin', method: 'GET')]
    #[OpenApi\Get(
        path: '/nuCoin',
        tags: ['NU Coin'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK')
        ]
    )]
    public function getAllNuCoins(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new NuCoinRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $count = $this->getContainer()
            ->get('mapper')
            ->withRequest($request)
            ->count(new NuCoin());

        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($request);

        return $this->hateoas(
            $request,
            $response,
            $page,
            $repository->paginate()
        );
    }

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
    #[Route('/nuCoin/{id}', method: 'GET')]
    #[OpenApi\Get(
        path: '/nuCoin/{id}',
        tags: ['NU Coin'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getNuCoinById(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new NuCoinRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $result = $repository->getById($args['id']);
        } catch (Throwable $e) {
            $result = null;
        }

        if (null === $result) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'NU coin data with id \'%s\' not found.',
                    $args['id']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $result);
    }

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
    #[Route('/nuCoin/{id}', method: 'PUT')]
    #[OpenApi\Put(
        path: '/nuCoin/{id}',
        tags: ['NU Coin'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function updateNuCoin(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new NuCoinSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assignOptional($schema);

        $repository = new NuCoinRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->update($args['id'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'NU coin data with id \'%s\' not found.',
                    $args['id']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity);
    }

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
    #[Route('/nuCoin/{id}', method: 'DELETE')]
    #[OpenApi\Delete(
        path: '/nuCoin/{id}',
        tags: ['NU Coin'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function deleteNuCoin(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new NuCoinRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->remove($args['id']);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'NU coin data with id \'%s\' not found.',
                    $args['id']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity);
    }

    #[Auth(
        role: [
            RoleType::ROOT, RoleType::ADMIN,
            RoleType::ADMIN_MASTER_DATA, RoleType::AGGREGATOR_ADMIN,
            RoleType::TASHARUF_ADMIN
        ]
    )]
    #[Route('/nuCoinBulk', method: 'POST')]
    #[OpenApi\Post(
        path: '/nuCoinBulk',
        tags: ['NU Coin'],
        responses: [
            new OpenApi\Response(response: 207, description: 'Multi Status')
        ]
    )]
    public function createBulkNuCoin(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $uploadedFiles = array_values($request->getUploadedFiles());

        if (
            sizeof($uploadedFiles) === 0 &&
            $request->getBody()->getContents() === ''
        ) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::BAD_REQUEST)
                ->withPair(
                    'message',
                    'NU Coin CSV data is not exist in both ' .
                    'uploaded files and stream body contents.'
                );

            return $this->json($response, $builder->build(), HttpCode::BAD_REQUEST);
        }

        $contents = sizeof($uploadedFiles) === 1
            ? $uploadedFiles[0]->getStream()->getContents()
            : $request->getBody()->getContents();

        $processor = new Processor();
        $processor->setRowLength(3);

        $schemas = $processor->transform(new NuCoinBulkSchema(), $contents);

        $repository = new NuCoinRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $nuCoinStates = $repository->createBulk($schemas, true);

        return $this->json($response, $nuCoinStates, HttpCode::MULTI_STATUS);
    }
}
