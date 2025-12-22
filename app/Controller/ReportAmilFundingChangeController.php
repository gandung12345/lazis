<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Lazis\Api\Repository\ReportAmilFundingChangeRepository;
use Lazis\Api\Schema\Report\ReportAmilFundingChangeSchema;
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
class ReportAmilFundingChangeController extends BaseController
{
    #[Route('/amilFundingChange/report', method: 'POST')]
    #[OpenApi\Post(
        path: '/amilFundingChange/report',
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
        $schema = new ReportAmilFundingChangeSchema();
        $schema->setYear(new ReportYearRangeSchema());

        $validator = new Validator();

        $validator->setRequest($request);
        $validator->assign($schema);

        $repository = new ReportAmilFundingChangeRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $report = ArrayHydrator::create()->hydrate(
            $repository->generateReport($schema)
        );

        return $this->json($response, $report, HttpCode::OK);
    }
}
