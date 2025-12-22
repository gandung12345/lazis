<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Lazis\Api\Repository\ReportAssetRecordingRepository;
use Lazis\Api\Schema\Report\ReportAssetRecordingSchema;
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
class ReportAssetRecordingController extends BaseController
{
    #[Route('/assetRecording/report', method: 'POST')]
    #[OpenApi\Post(
        path: '/assetRecording/report',
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
        $schema = new ReportAssetRecordingSchema();
        $schema->setYear(new ReportYearRangeSchema());

        $validator = new Validator();

        $validator->setRequest($request);
        $validator->assign($schema);

        $repository = new ReportAssetRecordingRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $report = ArrayHydrator::create()->hydrate(
            $repository->generateReport($schema)
        );

        return $this->json($response, $report, HttpCode::OK);
    }
}
