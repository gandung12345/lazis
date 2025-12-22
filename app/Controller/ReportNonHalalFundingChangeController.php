<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Lazis\Api\Repository\ReportNonHalalFundingChangeRepository;
use Lazis\Api\Schema\Report\ReportNonHalalFundingChangeSchema;
use Lazis\Api\Schema\Report\ReportYearRangeSchema;
use OpenApi\Attributes as OpenApi;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Schnell\Attribute\Route;
use Schnell\Http\Code as HttpCode;
use Schnell\Hydrator\ArrayHydrator;
use Schnell\Validator\Validator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ReportNonHalalFundingChangeController extends BaseController
{
    #[Route('/nonHalalFundingChange/report', method: 'POST')]
    #[OpenApi\Post(
        path: '/nonHalalFundingChange/report',
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
        $schema = new ReportNonHalalFundingChangeSchema();
        $schema->setYear(new ReportYearRangeSchema());

        $validator = new Validator();

        $validator->setRequest($request);
        $validator->assign($schema);

        $repository = new ReportNonHalalFundingChangeRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $report = ArrayHydrator::create()->hydrate(
            $repository->generateReport($schema)
        );

        return $this->json($response, $report, HttpCode::OK);
    }
}
