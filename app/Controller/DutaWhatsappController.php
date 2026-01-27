<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Throwable;
use Lazis\Api\Entity\DutaWhatsapp;
use Lazis\Api\Repository\DutaWhatsappRepository;
use Lazis\Api\Schema\DutaWhatsappSchema;
use Schnell\Attribute\Route;
use Schnell\Http\Code as HttpCode;
use Schnell\Paginator\Paginator;
use Schnell\Validator\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DutaWhatsappController extends BaseController
{
    #[Route('/dutaWhatsapp', method: 'GET')]
    public function getAllDws(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new DutaWhatsappRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $count = $this->getContainer()
            ->get('mapper')
            ->withRequest($request)
            ->count(new DutaWhatsapp());

        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($request);

        return $this->hateoas(
            $request,
            $response,
            $page,
            $repository->paginate()
        );
    }

    #[Route('/dutaWhatsapp', method: 'POST')]
    public function createDw(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new DutaWhatsappSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $repository = new DutaWhatsappRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        return $this->json(
            $response,
            $repository->create($schema),
            HttpCode::CREATED
        );
    }

    #[Route('/dutaWhatsapp/{id}', method: 'POST')]
    public function getDwById(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new DutaWhatsappRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        try {
            $result = $repository->getById($args['id'], true);
        } catch (Throwable $e) {
            $result = null;
        }

        if (null === $result) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::NOT_FOUND)
                ->withPair('message', sprintf(
                    'Duta whatsapp configuration with id \'%s\' not found.',
                    $args['id']
                ));

            return $this->json($response, $builder->build(), HttpCode::NOT_FOUND);
        }

        return $this->json($response, $result);
    }

    #[Route('/dutaWhatsapp/latest', method: 'GET')]
    public function getLatestGatewayConfig(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $repository = new DutaWhatsappRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        return $this->json($response, $repository->getLatest(true));
    }
}
