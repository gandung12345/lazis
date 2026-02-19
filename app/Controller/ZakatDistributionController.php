<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Throwable;
use Lazis\Api\Csv\Processor;
use Lazis\Api\Entity\ZakatDistribution;
use Lazis\Api\Http\Response\Builder as ResponseBuilder;
use Lazis\Api\Repository\ZakatDistributionRepository;
use Lazis\Api\Schema\ZakatDistributionSchema;
use Lazis\Api\Schema\ZakatDistributionBulkSchema;
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
class ZakatDistributionController extends BaseController
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
    #[Route('/zakatDistribution', method: 'GET')]
    #[OpenApi\Get(
        path: '/zakatDistribution',
        tags: ['Zakat Distribution'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK')
        ]
    )]
    public function getAllZakatDistributions(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new ZakatDistributionRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $count = $this->getContainer()
            ->get('mapper')
            ->withRequest($request)
            ->count(new ZakatDistribution());

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
    #[Route('/zakatDistribution/{id}', method: 'GET')]
    #[OpenApi\Get(
        path: '/zakatDistribution/{id}',
        tags: ['Zakat Distribution'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getZakatDistributionById(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new ZakatDistributionRepository(
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
                    'Zakat distribution data with id \'%s\' not found.',
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
    #[Route('/zakatDistribution/{id}', method: 'PUT')]
    #[OpenApi\Put(
        path: '/zakatDistribution/{id}',
        tags: ['Zakat Distribution'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function updateZakatDistribution(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new ZakatDistributionSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assignOptional($schema);

        $repository = new ZakatDistributionRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->update($args['id'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Zakat distribution data with id \'%s\' not found.',
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
    #[Route('/zakatDistribution/{id}', method: 'DELETE')]
    #[OpenApi\Delete(
        path: '/zakatDistribution/{id}',
        tags: ['Zakat Distribution'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function deleteZakatDistribution(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new ZakatDistributionRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->remove($args['id']);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Zakat distribution data with id \'%s\' not found.',
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
    #[Route('/zakatDistributionBulk', method: 'POST')]
    #[OpenApi\Post(
        path: '/zakatDistributionBulk',
        tags: ['Zakat Distribution'],
        responses: [
            new OpenApi\Response(response: 207, description: 'Multi Status')
        ]
    )]
    public function createBulkZakatDistribution(
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
                    'Zakat distribution CSV data is not exist in both ' .
                    'uploaded files and stream body contents.'
                );

            return $this->json($response, $builder->build(), HttpCode::BAD_REQUEST);
        }

        $contents = sizeof($uploadedFiles) === 1
            ? $uploadedFiles[0]->getStream()->getContents()
            : $request->getBody()->getContents();

        $processor = new Processor();
        $processor->setRowLength(5);

        $schemas = $processor->transform(new ZakatDistributionBulkSchema(), $contents);

        $repository = new ZakatDistributionRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $zakatDistributionStates = $repository->createBulk(
            $schemas,
            true
        );

        return $this->json($response, $zakatDistributionStates, HttpCode::MULTI_STATUS);
    }
}
