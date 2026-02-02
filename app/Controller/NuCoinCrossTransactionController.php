<?php

declare(strict_types=1);

namespace Lazis\Api\Controller;

use Lazis\Api\Notification\NotifierStrategy;
use Lazis\Api\Notification\Notifier\DutaWhatsappNotifier;
use Lazis\Api\Notification\Payload\DutaWhatsappPayload;
use Lazis\Api\Repository\MessageTemplateRepository;
use Lazis\Api\Repository\NuCoinCrossTransactionRepository;
use Lazis\Api\Repository\OrganizationRepository;
use Lazis\Api\Repository\DutaWhatsappRepository;
use Lazis\Api\Repository\Strategy\RepositoryStrategyInvocator;
use Lazis\Api\Schema\NuCoinCrossOrganizationTransferSchema;
use Lazis\Api\Sdk\SdkFactory;
use Lazis\Api\Type\NuCoinCrossTransactionIncomingStrategy as NuCoinCrossTransactionIncomingStrategyType;
use Lazis\Api\Type\NuCoinCrossTransactionOutgoingStrategy as NuCoinCrossTransactionOutgoingStrategyType;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Schnell\Attribute\Route;
use Schnell\Entity\EntityInterface;
use Schnell\Hydrator\MapHydrator;
use Schnell\Repository\RepositoryInterface;
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

        $organizationRepository = new OrganizationRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $dutaWhatsappRepository = new DutaWhatsappRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $gatewayConfig = $dutaWhatsappRepository->getLatest();

        if (null === $gatewayConfig) {
            $builder = new ResponseBuilder();
            $builder = $builder
                ->withPair('code', HttpCode::INTERNAL_SERVER_ERROR)
                ->withPair('message', 'NU coin cross transaction has been performed, but failed to notify the destination');

            return $this->json($response, $builder->build(), HttpCode::INTERNAL_SERVER_ERROR);
        }

        $source = $organizationRepository->getById($transferState->getSourceId());
        $destination = $organizationRepository->getById($transferState->getDestinationId());

        $sourcePhoneNumber = $source->getPhoneNumber();
        $destinationPhoneNumber = $destination->getPhoneNumber();

        if (strpos($sourcePhoneNumber, '62') !== 0) {
            $sourcePhoneNumber = strpos($sourcePhoneNumber, '0') === 0
                ? '62' . substr($sourcePhoneNumber, 1)
                : (strpos($sourcePhoneNumber, '+62') === 0
                    ? '62' . substr($sourcePhoneNumber, 3)
                    : false);
        }

        if (strpos($destinationPhoneNumber, '62') !== 0) {
            $destinationPhoneNumber = strpos($destinationPhoneNumber, '0') === 0
                ? '62' . substr($destinationPhoneNumber, 1)
                : (strpos($destinationPhoneNumber, '+62') === 0
                    ? '62' . substr($destinationPhoneNumber, 3)
                    : false);
        }

        $messageTemplateRepository = new MessageTemplateRepository(
            $this->getContainer()->get('mapper'),
            $request
        );

        $messageTemplates = $messageTemplateRepository->getAll();

        $message = $messageTemplates['nuCoin'];
        $message = str_replace('<sourceName>', $transferState->getSourceName(), $message);
        $message = str_replace('<destinationName>', $transferState->getDestinationName(), $message);
        $message = str_replace('<amount>', strval($transferState->getAmount()), $message);

        $payload = new DutaWhatsappPayload(
            $gatewayConfig->getApiKey(),
            $sourcePhoneNumber,
            $destinationPhoneNumber,
            $message
        );

        $factory = SdkFactory::create();
        $gateway = $factory->getDutaWhatsapp($gatewayConfig->getUrl());

        $notifierStrategy = new NotifierStrategy();
        $notifierStrategy->setNotifier(new DutaWhatsappNotifier($gateway));
        $notifierStrategy->notify($payload);

        return $this->json($response, MapHydrator::create()->hydrate($transferState));
    }

    /**
     * @param \Schnell\Entity\EntityInterface $entity
     * @param \Schnell\Repository\RepositoryInterface $repository
     * @return string
     */
    private function getNotificationMessage(
        EntityInterface $entity,
        RepositoryInterface $repository
    ): string {
        $result = null;
        $repositoryStrategyId = $repository
            ->getRepositoryStrategyInvocator()
            ->getRepositoryStrategy()
            ->repositoryStrategyId();

        switch ($repositoryStrategyId) {
            case 'nu-coin-incoming-transaction':
                $repositoryStrategyType = $repository
                    ->getRepositoryStrategyInvocator()
                    ->getRepositoryStrategy()
                    ->getIncomingStrategyType();

                if ($repositoryStrategyType === NuCoinCrossTransactionIncomingStrategyType::BRANCH_REPRESENTATIVE_FROM_BRANCH) {
                    $result = $this
                        ->getConfig()
                        ->get('nu-coin-template-message.branchToBranchRepresentative');
                } elseif ($repositoryStrategyType === NuCoinCrossTransactionIncomingStrategyType::TWIG_FROM_BRANCH_REPRESENTATIVE) {
                    $result = $this
                        ->getConfig()
                        ->get('nu-coin-template-message.branchRepresentativeToTwig');
                }

                break;
            case 'nu-coin-outgoing-transaction':
                $repositoryStrategyType = $repository
                    ->getRepositoryStrategyInvocator()
                    ->getRepositoryStrategy()
                    ->getOutgoingStrategyType();

                if ($repositoryStrategyType === NuCoinCrossTransactionOutgoingStrategyType::BRANCH_REPRESENTATIVE_TO_BRANCH) {
                    $result = $this
                        ->getConfig()
                        ->get('nu-coin-template-message.branchRepresentativeToBranch');
                } elseif ($repositoryStrategyType === NuCoinCrossTransactionOutgoingStrategyType::TWIG_TO_BRANCH_REPRESENTATIVE) {
                    $result = $this
                        ->getConfig()
                        ->get('nu-coin-template-message.twigToBranchRepresentative');
                }

                break;
        }

        $result = str_replace('<sourceName>', $entity->getSourceName(), $result);
        $result = str_replace('<destinationName>', $entity->getDestinationName(), $result);
        $result = str_replace('<amount>', strval($entity->getAmount()), $result);

        return $result;
    }
}
