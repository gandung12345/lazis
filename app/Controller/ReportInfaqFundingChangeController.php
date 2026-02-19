<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Lazis\Api\Repository\ReportInfaqFundingChangeRepository;
use Lazis\Api\Schema\Report\ReportInfaqFundingChangeSchema;
use Lazis\Api\Schema\Report\ReportYearRangeSchema;
use Lazis\Api\Type\Role as RoleType;
use OpenApi\Attributes as OpenApi;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Schnell\Attribute\Auth\Auth;
use Schnell\Attribute\Route;
use Schnell\Http\Code as HttpCode;
use Schnell\Hydrator\ArrayHydrator;
use Schnell\Validator\Validator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ReportInfaqFundingChangeController extends BaseController
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
    #[Route('/infaqFundingChange/report', method: 'POST')]
    #[OpenApi\Post(
        path: '/infaqFundingChange/report',
        tags: ['Report'],
        responses: [
            new OpenApi\Response(response: 200, description: 'OK')
        ]
    )]
    public function getReport(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new ReportInfaqFundingChangeSchema();
        $schema->setYear(new ReportYearRangeSchema());

        $validator = new Validator();

        $validator->setRequest($request);
        $validator->assign($schema);

        $repository = new ReportInfaqFundingChangeRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $report = ArrayHydrator::create()->hydrate(
            $repository->generateReport($schema)
        );

        return $this->json($response, $report, HttpCode::OK);
    }
}
