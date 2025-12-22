<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Lazis\Api\Repository\NuCoinCrossTransactionRepository;
use Lazis\Api\Repository\Strategy\RepositoryStrategyInvocator;
use Lazis\Api\Schema\NuCoinCrossOrganizationTransferSchema;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Schnell\Attribute\Route;
use Schnell\Hydrator\MapHydrator;
use Schnell\Validator\Validator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinCrossTransactionController extends BaseController
{
    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[Route('/nuCoinCrossTransaction', method: 'POST')]
    public function createCrossTransaction(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new NuCoinCrossOrganizationTransferSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new NuCoinCrossTransactionRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $repositoryStrategyInvocator = new RepositoryStrategyInvocator(
            $this->getContainer()->get('mapper'),
            $request
        );

        $repository->setRepositoryStrategyInvocator($repositoryStrategyInvocator);

        $transferState = $repository->createTransaction($schema);

        if (null === $transferState) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::BAD_REQUEST)
                ->withPair('message', 'NU cross organization transaction failed.');

            return $this->json($response, $builder->build(), HttpCode::BAD_REQUEST);
        }

        return $this->json($response, MapHydrator::create()->hydrate($transferState));
    }
}
