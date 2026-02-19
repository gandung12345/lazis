<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Lazis\Api\Repository\ReportOffBalanceSheetAggregateRepository;
use Lazis\Api\Schema\Report\ReportOffBalanceSheetAggregateSchema;
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
class ReportOffBalanceSheetAggregationController extends BaseController
{
    #[Auth(
        role: [
            RoleType::ROOT, RoleType::ADMIN,
            RoleType::ADMIN_MASTER_DATA, RoleType::AGGREGATOR_ADMIN,
            RoleType::TASHARUF_ADMIN
        ]
    )]
    #[Route('/offBalanceSheetAggregation/report', method: 'POST')]
    #[OpenApi\Post(
        path: '/offBalanceSheetAggregation/report',
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
        $schema = new ReportOffBalanceSheetAggregateSchema();
        $schema->setYear(new ReportYearRangeSchema());

        $validator = new Validator();

        $validator->setRequest($request);
        $validator->assign($schema);

        $repository = new ReportOffBalanceSheetAggregateRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $report = ArrayHydrator::create()->hydrate(
            $repository->generateReport($schema)
        );

        return $this->json($response, $report, HttpCode::OK);
    }
}
