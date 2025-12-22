<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Throwable;
use Lazis\Api\Entity\Amil;
use Lazis\Api\Http\Response\Builder as ResponseBuilder;
use Lazis\Api\Repository\AmilRepository;
use Lazis\Api\Repository\AmilFundingRepository;
use Lazis\Api\Repository\DsklRepository;
use Lazis\Api\Repository\InfaqRepository;
use Lazis\Api\Repository\ZakatRepository;
use Lazis\Api\Schema\AmilSchema;
use Lazis\Api\Schema\AmilFundingSchema;
use Lazis\Api\Schema\DsklSchema;
use Lazis\Api\Schema\InfaqSchema;
use Lazis\Api\Schema\ZakatSchema;
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
class AmilController extends BaseController
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
    #[Route('/amil', method: 'GET')]
    #[OpenApi\Get(
        path: '/amil',
        tags: ['Amil'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK')
        ]
    )]
    public function getAllAmils(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new AmilRepository(
            $this->getContainer()->get('mapper'),
            $request
        );
        $count = $this->getContainer()
            ->get('mapper')
            ->withRequest($request)
            ->count(new Amil());
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
    #[Route('/amil/{id}', method: 'GET')]
    #[OpenApi\Get(
        path: '/amil/{id}',
        tags: ['Amil'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getAmilById(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new AmilRepository(
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
                    'Amil with id \'%s\' not found.',
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
            RoleType::ADMIN_MASTER_DATA
        ]
    )]
    #[Route('/amil/{id}', method: 'PUT')]
    #[OpenApi\Put(
        path: '/amil/{id}',
        tags: ['Amil'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function updateAmil(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new AmilSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assignOptional($schema);

        $repository = new AmilRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->update($args['id'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Amil with id \'%s\' not found.',
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
            RoleType::ADMIN_MASTER_DATA
        ]
    )]
    #[Route('/amil/{id}', method: 'DELETE')]
    #[OpenApi\Delete(
        path: '/amil/{id}',
        tags: ['Amil'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function deleteAmil(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new AmilRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->remove($args['id']);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Amil with id \'%s\' not found.',
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
            RoleType::AGGREGATOR_ADMIN
        ]
    )]
    #[Route('/amil/{aid}/amilFunding', method: 'POST')]
    #[OpenApi\Post(
        path: '/amil/{aid}/amilFunding',
        tags: ['Amil'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createAmilFunding(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new AmilFundingSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new AmilFundingRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['aid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Amil with id \'%s\' not found.',
                    $args['aid']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity, HttpCode::CREATED);
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
            RoleType::AGGREGATOR_ADMIN
        ]
    )]
    #[Route('/amil/{aid}/dskl', method: 'POST')]
    #[OpenApi\Post(
        path: '/amil/{aid}/dskl',
        tags: ['Amil'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createDskl(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new DsklSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new DsklRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['aid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Amil with id \'%s\' not found.',
                    $args['aid']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity, HttpCode::CREATED);
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
            RoleType::AGGREGATOR_ADMIN
        ]
    )]
    #[Route('/amil/{aid}/infaq', method: 'POST')]
    #[OpenApi\Post(
        path: '/amil/{aid}/infaq',
        tags: ['Amil'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createInfaq(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new InfaqSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new InfaqRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['aid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Amil with id \'%s\' not found.',
                    $args['aid']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity, HttpCode::CREATED);
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
            RoleType::AGGREGATOR_ADMIN
        ]
    )]
    #[Route('/amil/{aid}/zakat', method: 'POST')]
    #[OpenApi\Post(
        path: '/amil/{aid}/zakat',
        tags: ['Amil'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createZakat(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new ZakatSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new ZakatRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['aid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Amil with id \'%s\' not found.',
                    $args['aid']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity, HttpCode::CREATED);
    }
}
