<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Lazis\Api\Repository\DashboardRepository;
use Lazis\Api\Schema\DashboardSchema;
use Schnell\Attribute\Route;
use Schnell\Http\Code as HttpCode;
use Schnell\Hydrator\MapHydrator;
use Schnell\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DashboardController extends BaseController
{
    use ControllerTrait;

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[Route('/dashboard/statistics', method: 'POST')]
    public function getStatistics(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new DashboardSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new DashboardRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $statistics = $repository->getStatistics($schema);

        return $this->json(
            $response,
            MapHydrator::create()->hydrate($statistics),
            HttpCode::OK
        );
    }
}
