<?php

namespace Lazis\Api\Controller;

use Lazis\Api\Auth\Authorization;
use Lazis\Api\Http\Response\Builder as ResponseBuilder;
use Lazis\Api\Schema\TokenSchema;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Schnell\Attribute\Route;
use Schnell\Validator\Validator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class AuthorizationController extends BaseController
{
    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    #[Route('/token', method: 'POST')]
    public function createToken(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $schema = new TokenSchema();
        $validator = new Validator();
        $validator = $validator->withRequest($request);
        $validator->assign($schema);

        $authorization = new Authorization(
            $this->getContainer(),
            $this->getConfig(),
            $schema
        );

        $tokenObject = $authorization->authorize($request);
        $builder = new ResponseBuilder();
        $builder = $builder->withPair('accessToken', $tokenObject->getToken());

        return $this->json($response, $builder->build());
    }
}
