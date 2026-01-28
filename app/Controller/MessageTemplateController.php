<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Lazis\Api\Repository\MessageTemplateRepository;
use Lazis\Api\Schema\MessageTemplateSchema;
use Schnell\Attribute\Route;
use Schnell\Http\Code as HttpCode;
use Schnell\Validator\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

use function json_decode;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class MessageTemplateController extends BaseController
{
    #[Route('/messageTemplate', method: 'GET')]
    public function getAllMessageTemplate(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new MessageTemplateRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        return $this->json($response, $repository->getAll(), HttpCode::OK);
    }

    #[Route('/messageTemplate', method: 'POST')]
    public function createOrUpdateMessageTemplate(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new MessageTemplateSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new MessageTemplateRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        return $this->json($response, $repository->createOrUpdate($schema), HttpCode::OK);
    }
}
