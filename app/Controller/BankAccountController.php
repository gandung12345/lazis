<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Throwable;
use Lazis\Api\Entity\BankAccount;
use Lazis\Api\Http\Response\Builder as ResponseBuilder;
use Lazis\Api\Repository\BankAccountRepository;
use Lazis\Api\Schema\BankAccountSchema;
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
class BankAccountController extends BaseController
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
    #[Route('/bankAccount', method: 'GET')]
    #[OpenApi\Get(
        path: '/bankAccount',
        tags: ['Bank Account'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK')
        ]
    )]
    public function getAllBankAccounts(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new BankAccountRepository(
            $this->getContainer()->get('mapper'),
            $request
        );
        $count = $this->getContainer()
            ->get('mapper')
            ->withRequest($request)
            ->count(new BankAccount());
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
    #[Route('/bankAccount/{id}', method: 'GET')]
    #[OpenApi\Get(
        path: '/bankAccount/{id}',
        tags: ['Bank Account'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getBankAccountById(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new BankAccountRepository(
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
                    'Bank account data with id \'%s\' not found.',
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
    #[Route('/bankAccount/{id}', method: 'PUT')]
    #[OpenApi\Put(
        path: '/bankAccount/{id}',
        tags: ['Bank Account'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function updateBankAccount(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new BankAccountSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assignOptional($schema);

        $repository = new BankAccountRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->update($args['id'], $schema);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Bank account data with id \'%s\' not found.',
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
    #[Route('/bankAccount/{id}', method: 'DELETE')]
    #[OpenApi\Delete(
        path: '/bankAccount/{id}',
        tags: ['Bank Account'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function deleteBankAccount(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new BankAccountRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $entity = $repository->remove($args['id']);

        if (null === $entity) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Bank account data with id \'%s\' not found.',
                    $args['id']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $entity);
    }
}
