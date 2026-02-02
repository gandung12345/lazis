<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Throwable;
use Lazis\Api\Xlsx\Processor as XlsxProcessor;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Transaction;
use Lazis\Api\Http\Response\Builder as ResponseBuilder;
use Lazis\Api\Repository\AmilFundingUsageRepository;
use Lazis\Api\Repository\AssetRecordingRepository;
use Lazis\Api\Repository\BankAccountRepository;
use Lazis\Api\Repository\DoneeRepository;
use Lazis\Api\Repository\DutaWhatsappRepository;
use Lazis\Api\Repository\InfaqDistributionRepository;
use Lazis\Api\Repository\LegalRepository;
use Lazis\Api\Repository\MosqueRepository;
use Lazis\Api\Repository\NonHalalFundingDistributionRepository;
use Lazis\Api\Repository\NonHalalFundingReceiveRepository;
use Lazis\Api\Repository\OrganizationRepository;
use Lazis\Api\Repository\OrganizerRepository;
use Lazis\Api\Repository\TransactionRepository;
use Lazis\Api\Repository\VolunteerRepository;
use Lazis\Api\Repository\WalletRepository;
use Lazis\Api\Schema\AmilFundingUsageSchema;
use Lazis\Api\Schema\AssetRecordingSchema;
use Lazis\Api\Schema\BankAccountSchema;
use Lazis\Api\Schema\DoneeContextualRelationSchema;
use Lazis\Api\Schema\DoneeSchema;
use Lazis\Api\Schema\DoneeSerializationAwareSchema;
use Lazis\Api\Schema\InfaqDistributionSchema;
use Lazis\Api\Schema\InfaqDistributionBulkSchema;
use Lazis\Api\Schema\LegalSchema;
use Lazis\Api\Schema\MosqueSchema;
use Lazis\Api\Schema\NonHalalFundingDistributionSchema;
use Lazis\Api\Schema\NonHalalFundingReceiveSchema;
use Lazis\Api\Schema\OrganizationSchema;
use Lazis\Api\Schema\OrganizerSchema;
use Lazis\Api\Schema\TransactionSchema;
use Lazis\Api\Schema\VolunteerSchema;
use Lazis\Api\Schema\WalletSchema;
use Lazis\Api\Sdk\SdkFactory;
use Lazis\Api\Sdk\DutaWhatsapp\Payload\CreateDevice as CreateDevicePayload;
use Lazis\Api\Sdk\DutaWhatsapp\Payload\DeviceInfo as DeviceInfoPayload;
use Lazis\Api\Type\Role as RoleType;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Route;
use Schnell\Attribute\Auth\Auth;
use Schnell\Http\Code as HttpCode;
use Schnell\Paginator\Paginator;
use Schnell\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class OrganizationController extends BaseController
{
    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[Auth(role: [RoleType::ROOT, RoleType::ADMIN])]
    #[Route('/organization', method: 'GET')]
    #[OpenApi\Get(
        path: '/organization',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK')
        ]
    )]
    public function getAllOrganizations(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new OrganizationRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $count = $this->getContainer()
            ->get('mapper')
            ->withRequest($request)
            ->count(new Organization());

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
    #[Auth(role: [RoleType::ROOT])]
    #[Route('/organization', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created')
        ]
    )]
    public function createOrganization(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new OrganizationSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new OrganizationRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $result = $this->registerNumberToGateway($request, $schema->getPhoneNumber());
        } catch (Throwable $e) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', $e->getCode())
                ->withPair('message', $e->getMessage());

            return $this->json($response, $builder->build(), $e->getCode());
        }

        $hydratedEntity = $repository->create($schema);

        if (null === $result) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::INTERNAL_SERVER_ERROR)
                ->withPair('message', 'Invalid whatsapp gateway configuration.');

            return $this->json($response, $builder->build(), HttpCode::INTERNAL_SERVER_ERROR);
        }

        if ($result->getStatusCode() !== 200) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', $result->getStatusCode())
                ->withPair('message', $result->getReasonPhrase());

            return $this->json($response, $builder->build(), $result->getStatusCode());
        }

        return $this->json($response, $hydratedEntity, HttpCode::CREATED);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[Route('/organizationBulk', method: 'POST')]
    #[OpenApi\Post(
        path: '/organizationBulk',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 207, description: 'Multi Status')
        ]
    )]
    public function createBulkOrganization(
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
                    'Organization CSV data is not exist in both ' .
                    'uploaded files and stream body contents.'
                );

            return $this->json($response, $builder->build(), HttpCode::BAD_REQUEST);
        }

        $contents = sizeof($uploadedFiles) === 1
            ? $uploadedFiles[0]->getStream()->getContents()
            : $request->getBody()->getContents();

        $processor = new Processor();
        $processor->setRowLength(7);

        $schemas = $processor->transform(new OrganizationSchema(), $contents);

        $organizationRepository = new OrganizationRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $organizationStates = $organizationRepository->createBulk(
            $schemas,
            true
        );

        return $this->json($response, $organizationStates, HttpCode::MULTI_STATUS);
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
    #[Route('/organization/{id}', method: 'GET')]
    #[OpenApi\Get(
        path: '/organization/{id}',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getOrganizationById(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new OrganizationRepository(
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
                    'Organization with id \'%s\' not found.',
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
    #[Auth(role: [RoleType::ROOT, RoleType::ADMIN])]
    #[Route('/organization/{id}', method: 'PUT')]
    #[OpenApi\Put(
        path: '/organization/{id}',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function updateOrganization(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new OrganizationSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assignOptional($schema);

        $repository = new OrganizationRepository(
            $this->getContainer()->get('mapper'),
            $request
        );
        $entity = $repository->update($args['id'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
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
    #[Auth(role: [RoleType::ROOT])]
    #[Route('/organization/{id}', method: 'DELETE')]
    #[OpenApi\Delete(
        path: '/organization/{id}',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function deleteOrganization(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new OrganizationRepository(
            $this->getContainer()->get('mapper'),
            $request
        );
        $entity = $repository->remove($args['id']);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
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
    #[Auth(role: [RoleType::ROOT, RoleType::ADMIN])]
    #[Route('/organization/{oid}/bankAccount', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/bankAccount',
        tags: ['Organization'],
        description: 'Create bank account',
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createBankAccount(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new BankAccountSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new BankAccountRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['oid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
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
    #[Auth(role: [RoleType::ROOT, RoleType::ADMIN])]
    #[Route('/organization/{oid}/legal', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/legal',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createLegal(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new LegalSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new LegalRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['oid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
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
    #[Auth(role: [RoleType::ROOT, RoleType::ADMIN])]
    #[Route('/organization/{oid}/organizer', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/organizer',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createOrganizer(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new OrganizerSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new OrganizerRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['oid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity, HttpCode::CREATED);
    }

    private function registerNumberToGateway(Request $request, string $phoneNumber): Response
    {
        $dutaWhatsappRepository = new DutaWhatsappRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $gatewayConfig = $dutaWhatsappRepository->getLatest();

        if (null === $gatewayConfig) {
            return null;
        }

        $sdkFactory = SdkFactory::create();
        $dutaWhatsappGateway = $sdkFactory->getDutaWhatsapp($gatewayConfig->getUrl());

        $deviceInfo = new DeviceInfoPayload(
            $gatewayConfig->getApiKey(),
            $this->normalizePhoneNumber($phoneNumber)
        );

        try {
            $deviceInfoResponse = $dutaWhatsappGateway->deviceInfo($deviceInfo);
            $responseCode = $deviceInfoResponse->getStatusCode();
        } catch (Throwable $e) {
            $responseCode = $e->getCode();
        }

        if ($responseCode === HttpCode::BAD_REQUEST) {
            $createDevice = new CreateDevicePayload(
                $gatewayConfig->getApiKey(),
                $this->normalizePhoneNumber($phoneNumber),
                ''
            );

            return $dutaWhatsappGateway->createDevice($createDevice);
        }

        return $deviceInfoResponse;
    }

    private function normalizePhoneNumber(string $phoneNumber): string
    {
        if (strpos($phoneNumber, '0') === 0) {
            return '62' . substr($phoneNumber, 1);
        }

        if (strpos($phoneNumber, '+62') === 0) {
            return '62' . substr($phoneNumber, 3);
        }

        return $phoneNumber;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[Route('/organization/{oid}/organizerBulk', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/organizer',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 207, description: 'Multi Status'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createBulkOrganizer(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $organizationRepository = new OrganizationRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $organization = $organizationRepository->getById($args['oid'], false);
        } catch (Throwable $e) {
            $organization = null;
        }

        if (null === $organization) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
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
                    'Organizer CSV data is not exist in both ' .
                    'uploaded files and stream body contents.'
                );

            return $this->json($response, $builder->build(), HttpCode::BAD_REQUEST);
        }

        $contents = sizeof($uploadedFiles) === 1
            ? $uploadedFiles[0]->getStream()->getContents()
            : $request->getBody()->getContents();

        $processor = XlsxProcessor::create($contents);
        $schemas   = $processor->transform(new OrganizerSchema());

        $organizerRepository = new OrganizerRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $organizerStates = $organizerRepository->createBulk(
            $args['oid'],
            $organization,
            $schemas,
            true
        );

        return $this->json($response, $organizerStates, HttpCode::MULTI_STATUS);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[Auth(role: [RoleType::ROOT, RoleType::ADMIN])]
    #[Route('/organization/{oid}/volunteer', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/volunteer',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createVolunteer(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new VolunteerSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new VolunteerRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['oid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
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
    #[Route('/organization/{oid}/volunteerBulk', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/volunteerBulk',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 207, description: 'Multi Status'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createBulkVolunteer(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $organizationRepository = new OrganizationRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $organization = $organizationRepository->getById($args['oid'], false);
        } catch (Throwable $e) {
            $organization = null;
        }

        if (null === $organization) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found',
                    $args['oid']
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
                    'Volunteer XLSX data is not exist in both ' .
                    'uploaded files and stream body contents.'
                );

            return $this->json($response, $builder->build(), HttpCode::BAD_REQUEST);
        }

        $contents = sizeof($uploadedFiles) === 1
            ? $uploadedFiles[0]->getStream()->getContents()
            : $request->getBody()->getContents();

        $processor = XlsxProcessor::create($contents);
        $schemas   = $processor->transform(new VolunteerSchema());

        $volunteerRepository = new VolunteerRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $volunteerStates = $volunteerRepository->createBulk(
            $args['oid'],
            $organization,
            $schemas,
            true
        );

        return $this->json($response, $volunteerStates, HttpCode::MULTI_STATUS);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[Auth(role: [RoleType::ROOT, RoleType::ADMIN])]
    #[Route('/organization/{oid}/donee', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/donee',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createDonee(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new DoneeSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new DoneeRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['oid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity, HttpCode::CREATED);
    }

    #[Route('/organization/{oid}/doneeBulk', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/doneeBulk',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 207, description: 'Multi Status'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createBulkDonee(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $organizationRepository = new OrganizationRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $organization = $organizationRepository->getById($args['oid'], false);
        } catch (Throwable $e) {
            $organization = null;
        }

        if (null === $organization) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
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
                    'Donee XLSX data is not exist in both ' .
                    'uploaded files and stream body contents.'
                );

            return $this->json($response, $builder->build(), HttpCode::BAD_REQUEST);
        }

        $contents = sizeof($uploadedFiles) === 1
            ? $uploadedFiles[0]->getStream()->getContents()
            : $request->getBody()->getContents();

        $processor = XlsxProcessor::create($contents);
        $schemas   = $processor->transform(new DoneeSchema());

        $doneeRepository = new DoneeRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $doneeStates = $doneeRepository->createBulk(
            $args['oid'],
            $organization,
            $schemas,
            true
        );

        return $this->json($response, $doneeStates, HttpCode::MULTI_STATUS);
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
            RoleType::AGGREGATOR_ADMIN, RoleType::TASHARUF_ADMIN
        ]
    )]
    #[Route('/organization/{oid}/transaction', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/transaction',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createTransaction(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new TransactionSchema();
        $schema->setWallet(new WalletSchema());

        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new TransactionRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['oid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
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
    #[Auth(role: [RoleType::ROOT, RoleType::ADMIN])]
    #[Route('/organization/{oid}/mosque', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/mosque',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createMosque(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new MosqueSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new MosqueRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['oid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
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
    #[Route('/organization/{oid}/mosqueBulk', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/mosqueBulk',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 207, description: 'Multi Status'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createBulkMosque(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $organizationRepository = new OrganizationRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $organization = $organizationRepository->getById($args['oid'], false);
        } catch (Throwable $e) {
            $organization = null;
        }

        if (null === $organization) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
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
                    'Mosque CSV data is not exist in both ' .
                    'uploaded files and stream body contents.'
                );

            return $this->json($response, $builder->build(), HttpCode::BAD_REQUEST);
        }

        $contents = sizeof($uploadedFiles) === 1
            ? $uploadedFiles[0]->getStream()->getContents()
            : $request->getBody()->getContents();

        $processor = new Processor();
        $processor->setRowLength(13);

        $schemas = $processor->transform(new MosqueSchema(), $contents);

        $mosqueRepository = new MosqueRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $mosqueStates = $mosqueRepository->createBulk(
            $args['oid'],
            $organization,
            $schemas,
            true
        );

        return $this->json($response, $mosqueStates, HttpCode::MULTI_STATUS);
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
    #[Route('/organization/{oid}/wallet/{wid}/transaction', method: 'GET')]
    #[OpenApi\Get(
        path: '/organization/{oid}/wallet/{wid}/transaction',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getTransactionByWalletAndOrganization(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $organizationRepository = new OrganizationRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $walletRepository = new WalletRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $transactionRepository = new TransactionRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $organization = $organizationRepository->getById($args['oid']);
        } catch (Throwable $e) {
            $organization = null;
        }

        if (null === $organization) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        try {
            $wallet = $walletRepository->findOneWithParent(
                $organization,
                $args['wid'],
                false
            );
        } catch (Throwable $e) {
            $wallet = null;
        }

        if (null === $wallet) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Wallet with id \'%s\' and organization \'%s\' not found.',
                    $args['wid'],
                    $args['oid']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        $count = $this->getContainer()
            ->get('mapper')
            ->countByParent(new Transaction(), $wallet);

        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($request);

        return $this->hateoas(
            $request,
            $response,
            $page,
            $transactionRepository->paginateByParent($wallet)
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
            RoleType::AGGREGATOR_ADMIN
        ]
    )]
    #[Route('/organization/{oid}/nonHalalFundingReceive', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/nonHalalFundingReceive',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createNonHalalFundingReceive(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new NonHalalFundingReceiveSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new NonHalalFundingReceiveRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['oid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
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
            RoleType::TASHARUF_ADMIN
        ]
    )]
    #[Route('/organization/{oid}/nonHalalFundingDistribution', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/nonHalalFundingDistribution',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createNonHalalFundingDistribution(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new NonHalalFundingDistributionSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new NonHalalFundingDistributionRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['oid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
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
    #[Route('/organization/{oid}/infaqDistribution', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/infaqDistribution',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found'),
            new OpenApi\Response(response: 422, description: 'Unprocessable Entity')
        ]
    )]
    public function createInfaqDistribution(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $childSchema = $this->getDoneeSchemaByPrimaryKeyExistence($request);

        if (null === $childSchema) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::UNPROCESSABLE_ENTITY)
                ->withPair(
                    'message',
                    '\'@donee\' key must be exist and its value must be ' .
                    'JSON object in this JSON schema request.'
                );

            return $this->json($response, $builder->build(), HttpCode::UNPROCESSABLE_ENTITY);
        }

        $schema = new InfaqDistributionSchema();
        $schema->setDonee($childSchema);

        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new InfaqDistributionRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['oid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found.',
                    $args['oid']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity, HttpCode::CREATED);
    }

    #[Route('/organization/{oid}/infaqDistributionBulk', method: 'POST')]
    #[OpenApi\Post(
        path: '/infaqDistributionBulk',
        tags: ['Infaq Distribution'],
        responses: [
            new OpenApi\Response(response: 207, description: 'Multi Status')
        ]
    )]
    public function createBulkInfaqDistribution(
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
                    'Infaq distribution CSV data is not exist in both ' .
                    'uploaded files and stream body contents.'
                );

            return $this->json($response, $builder->build(), HttpCode::BAD_REQUEST);
        }

        $contents = sizeof($uploadedFiles) === 1
            ? $uploadedFiles[0]->getStream()->getContents()
            : $request->getBody()->getContents();

        $processor = new Processor();
        $processor->setRowLength(12);

        $schemas = $processor->transform(new InfaqDistributionBulkSchema(), $contents);

        $repository = new InfaqDistributionRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $infaqDistributionStates = $repository->createBulk(
            $args['oid'],
            $schemas,
            true
        );

        return $this->json($response, $infaqDistributionStates, HttpCode::MULTI_STATUS);
    }

    #[Route('/organization/{oid}/amilFundingUsage', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/amilFundingUsage',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createAmilFundingUsage(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new AmilFundingUsageSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new AmilFundingUsageRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['oid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found',
                    $args['oid']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity, HttpCode::CREATED);
    }

    #[Route('/organization/{oid}/assetRecording', method: 'POST')]
    #[OpenApi\Post(
        path: '/organization/{oid}/assetRecording',
        tags: ['Organization'],
        responses: [
            new OpenApi\Response(response: 201, description: 'Created'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function createAssetRecording(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new AssetRecordingSchema();
        $validator = Validator::create($request);

        $validator->assign($schema);

        $repository = new AssetRecordingRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->create($args['oid'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Organization with id \'%s\' not found',
                    $args['oid']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity, HttpCode::CREATED);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @return \Lazis\Api\Schema\DoneeSerializationAwareSchema|null
     */
    private function getDoneeSchemaByPrimaryKeyExistence(
        Request $request
    ): ?DoneeSerializationAwareSchema {
        $parsedBody = $request->getParsedBody();

        if (!isset($parsedBody['@donee'])) {
            return null;
        }

        return match (isset($parsedBody['@donee']['id'])) {
            true => new DoneeContextualRelationSchema(),
            false => new DoneeSchema()
        };
    }
}
