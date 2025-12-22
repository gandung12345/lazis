<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Throwable;
use Lazis\Api\Entity\Transaction;
use Lazis\Api\Entity\Wallet;
use Lazis\Api\Http\Response\Builder as ResponseBuilder;
use Lazis\Api\Repository\TransactionRepository;
use Lazis\Api\Repository\WalletRepository;
use Lazis\Api\Type\Role as RoleType;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Auth\Auth;
use Schnell\Attribute\Route;
use Schnell\Http\Code as HttpCode;
use Schnell\Paginator\Paginator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class WalletController extends BaseController
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
    #[Route('/wallet', method: 'GET')]
    #[OpenApi\Get(
        path: '/wallet',
        tags: ['Wallet'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK')
        ]
    )]
    public function getAllWallet(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new WalletRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $count = $this->getContainer()
            ->get('mapper')
            ->withRequest($request)
            ->count(new Wallet());

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
    #[Route('/wallet/{id}', method: 'GET')]
    #[OpenApi\Get(
        path: '/wallet/{id}',
        tags: ['Wallet'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getWalletById(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new WalletRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $result = $repository->getById($args['id']);

        if (null === $result) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Wallet with id \'%s\' not found.',
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
    #[Route('/wallet/{id}/transaction', method: 'GET')]
    #[OpenApi\Get(
        path: '/wallet/{id}/transaction',
        tags: ['Wallet'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK'),
            new OpenApi\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function getTransaction(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $transactionRepository = new TransactionRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $walletRepository = new WalletRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $wallet = $walletRepository->getById($request, $args['id'], false);
        } catch (Throwable $e) {
            $wallet = null;
        }

        if (null === $wallet) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Wallet with id \'%s\' not found.',
                    $args['id']
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
}
