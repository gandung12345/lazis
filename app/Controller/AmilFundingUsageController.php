<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Lazis\Api\Entity\AmilFundingUsage;
use Lazis\Api\Repository\AmilFundingUsageRepository;
use Lazis\Api\Schema\AmilFundingUsageSchema;
use Lazis\Api\Http\Response\Builder as ResponseBuilder;
use Lazis\Api\Type\Role as RoleType;
use OpenApi\Attributes as OpenApi;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Schnell\Attribute\Auth\Auth;
use Schnell\Attribute\Route;
use Schnell\Http\Code as HttpCode;
use Schnell\Paginator\Paginator;
use Schnell\Validator\Validator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class AmilFundingUsageController extends BaseController
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
    #[Route('/amilFundingUsage', method: 'GET')]
    #[OpenApi\Get(
        path: '/amilFundingUsage',
        tags: ['Amil Funding Usage'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK')
        ]
    )]
    public function getAllAmilFundingUsages(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new AmilFundingUsageRepository(
            $this->getContainer()->get('mapper'),
            $request
        );
        $count = $this->getContainer()
            ->get('mapper')
            ->withRequest($request)
            ->count(new AmilFundingUsage());
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
    #[Route('/amilFundingUsage/{id}', method: 'GET')]
    #[OpenApi\Get(
        path: '/amilFundingUsage/{id}',
        tags: ['Amil Funding Usage'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getAmilFundingUsageById(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new AmilFundingUsageRepository(
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
                    'Amil funding usage data with id \'%s\' not found.',
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
            RoleType::TASHARUF_ADMIN
        ]
    )]
    #[Route('/amilFundingUsage/{id}', method: 'PUT')]
    #[OpenApi\Get(
        path: '/amilFundingUsage/{id}',
        tags: ['Amil Funding Usage'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function updateAmilFundingUsage(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new AmilFundingUsageSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assignOptional($schema);

        $repository = new AmilFundingUsageRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->update($args['id'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Amil funding usage data with id \'%s\' not found.',
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
            RoleType::ADMIN_MASTER_DATA, RoleType::TASHARUF_ADMIN
        ]
    )]
    #[Route('/amilFundingUsage/{id}', method: 'DELETE')]
    #[OpenApi\Delete(
        path: '/amilFundingUsage/{id}',
        tags: ['Amil Funding Usage'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function deleteAmilFundingUsage(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new AmilFundingUsageRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->remove($args['id']);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Amil funding usage data with id \'%s\' not found.',
                    $args['id']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity);
    }
}
