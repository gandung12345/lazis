<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Lazis\Api\Xlsx\Processor as XlsxProcessor;
use Lazis\Api\Xlsx\Writer as XlsxWriter;
use Lazis\Api\Xlsx\CallableWriter\NuCoinAggregatorCallableWriter;
use Lazis\Api\Entity\Donor;
use Lazis\Api\Entity\Volunteer;
use Lazis\Api\Http\Response\Builder as ResponseBuilder;
use Lazis\Api\Repository\DonorRepository;
use Lazis\Api\Repository\VolunteerRepository;
use Lazis\Api\Schema\DonorSchema;
use Lazis\Api\Schema\VolunteerSchema;
use Lazis\Api\Type\Role as RoleType;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Auth\Auth;
use Schnell\Attribute\Route;
use Schnell\Http\Code as HttpCode;
use Schnell\Paginator\Paginator;
use Schnell\Schema\SchemaInterface;
use Schnell\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class VolunteerController extends BaseController
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
    #[Route('/volunteer', method: 'GET')]
    #[OpenApi\Get(
        path: '/volunteer',
        tags: ['Volunteer'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK')
        ]
    )]
    public function getAllVolunteers(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new VolunteerRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $count = $this->getContainer()
            ->get('mapper')
            ->withRequest($request)
            ->count(new Volunteer());

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
    #[Route('/volunteer/{id}', method: 'GET')]
    #[OpenApi\Get(
        path: '/volunteer/{id}',
        tags: ['Volunteer'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getVolunteerById(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new VolunteerRepository(
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
                    'Volunteer with id \'%s\' not found.',
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
    #[Route('/volunteer/{id}', method: 'PUT')]
    #[OpenApi\Put(
        path: '/volunteer/{id}',
        tags: ['Volunteer'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function updateVolunteer(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new VolunteerSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assignOptional($schema);

        $repository = new VolunteerRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->update($args['id'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Volunteer with id \'%s\' not found.',
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
    #[Route('/volunteer/{id}', method: 'DELETE')]
    #[OpenApi\Delete(
        path: '/volunteer/{id}',
        tags: ['Volunteer'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function deleteVolunteer(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new VolunteerRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->remove($args['id']);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Volunteer with id \'%s\' not found.',
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
    #[Route('/volunteer/{vid}/donor', method: 'GET')]
    #[OpenApi\Get(
        path: '/volunteer/{vid}/donor',
        tags: ['Volunteer'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getDonor(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $donorRepository = new DonorRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $volunteerRepository = new VolunteerRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $volunteer = $volunteerRepository->getById($args['vid'], false);
        } catch (Throwable $e) {
            $volunteer = null;
        }

        if (null === $volunteer) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Volunteer with id \'%s\' not found.',
                    $args['vid']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        $count = $this->getContainer()
            ->get('mapper')
            ->countByParent(new Donor(), $volunteer);

        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($request);

        return $this->hateoas(
            $request,
            $response,
            $page,
            $donorRepository->paginateByParent($volunteer)
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
    #[Route('/volunteer/{vid}/donor', method: 'POST')]
    #[OpenApi\Post(
        path: '/volunteer/{vid}/donor',
        tags: ['Volunteer'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createDonor(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new DonorSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new DonorRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['vid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Volunteer with id \'%s\' not found.',
                    $args['vid']
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
            RoleType::ADMIN_MASTER_DATA, RoleType::AGGREGATOR_ADMIN,
            RoleType::TASHARUF_ADMIN
        ]
    )]
    #[Route('/volunteer/{vid}/type/{vtype}/donor', method: 'GET')]
    #[OpenApi\Get(
        path: '/volunteer/{vid}/type/{vtype}/donor',
        tags: ['Volunteer'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK')
        ]
    )]
    public function getDonorByVolunteerIdAndType(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new DonorRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $count = $repository->countByVolunteerIdAndType(
            $args['vid'],
            $args['vtype']
        );

        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($request);

        $result = $repository->paginateByVolunteerIdAndType(
            $args['vid'],
            $args['vtype']
        );

        return $this->hateoas(
            $request,
            $response,
            $page,
            $result
        );
    }

    #[Auth(
        role: [
            RoleType::ROOT, RoleType::ADMIN,
            RoleType::ADMIN_MASTER_DATA, RoleType::AGGREGATOR_ADMIN,
            RoleType::TASHARUF_ADMIN
        ]
    )]
    #[Route('/organizationContext/{oid}/volunteer/{vid}/type/{vtype}/donor/download', method: 'POST')]
    #[OpenApi\Post(
        path: '/volunteer/{vid}/type/{vtype}/donor/download',
        tags: ['Volunteer'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 400, description: 'Bad Request')
        ]
    )]
    public function fillDonorDataToTransferSheet(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $uploadedFiles = array_values($request->getUploadedFiles());

        if (sizeof($uploadedFiles) === 0 && $request->getBody()->getContents() === '') {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::BAD_REQUEST)
                ->withPair(
                    'message',
                    'Nu coin aggregator transfer template sheet is not exist in both ' .
                    'uploaded files and stream body contents.'
                );

            return $this->json($response, $builder->build(), HttpCode::BAD_REQUEST);
        }

        $contents = sizeof($uploadedFiles) === 1
            ? $uploadedFiles[0]->getStream()->getContents()
            : $request->getBody()->getContents();

        $repository = new DonorRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $results = $repository->fetchDonorSheetData(
            $args['oid'],
            $args['vid'],
            $args['vtype']
        );

        return XlsxWriter::create($contents)->write($results, new NuCoinAggregatorCallableWriter());
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
    #[Route('/volunteer/{vid}/donorBulk', method: 'POST')]
    #[OpenApi\Post(
        path: '/volunteer/{vid}/donorBulk',
        tags: ['Volunteer'],
        responses: [
            new OpenApi\Response(response: 207, description: 'Multi Status'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createBulkDonor(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $volunteerRepository = new VolunteerRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $volunteer = $volunteerRepository->getById($args['vid'], false);
        } catch (Throwable $e) {
            $volunteer = null;
        }

        if (null === $volunteer) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Volunteer with id \'%s\' not found',
                    $args['vid']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

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
                    'Donor CSV data is not exist in both ' .
                    'uploaded files and stream body contents.'
                );

            return $this->json($response, $builder->build(), HttpCode::BAD_REQUEST);
        }

        $contents = sizeof($uploadedFiles) === 1
            ? $uploadedFiles[0]->getStream()->getContents()
            : $request->getBody()->getContents();

        $processor = XlsxProcessor::create($contents);
        $schemas   = $processor->transform(new DonorSchema());

        $donorRepository = new DonorRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $donorStates = $donorRepository->createBulk(
            $args['vid'],
            $volunteer,
            $schemas,
            true
        );

        return $this->json($response, $donorStates, HttpCode::MULTI_STATUS);
    }
}
