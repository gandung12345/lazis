<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Throwable;
use Lazis\Api\Entity\Donee;
use Lazis\Api\Entity\InfaqDistribution;
use Lazis\Api\Entity\InfaqDistributionBulkResponseState;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Repository\Exception\RepositoryException;
use Lazis\Api\Schema\DoneeContextualRelationSchema;
use Lazis\Api\Schema\InfaqDistributionSchema;
use Lazis\Api\Schema\TransactionSchema;
use Lazis\Api\Schema\WalletSchema;
use Lazis\Api\Type\Transaction as TransactionType;
use Lazis\Api\Type\Wallet as WalletType;
use Lazis\Api\Type\ZakatDistribution as ZakatDistributionType;
use Schnell\Entity\EntityInterface;
use Schnell\Http\Code as HttpCode;
use Schnell\Hydrator\ArrayHydrator;
use Schnell\Hydrator\MapHydrator;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class InfaqDistributionRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new InfaqDistribution());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new InfaqDistribution());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new InfaqDistribution(), $id);

        if (null === $entity) {
            return null;
        }

        return $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|array
     */
    public function create(
        $refId,
        SchemaInterface $schema,
        bool $hydrated = true
    ): EntityInterface|array|null {
        if ($schema->getAmount() > 0) {
            throw new RepositoryException(
                $this->getRequest(),
                "Infaq distribution amount must be equal or less than zero. " .
                "Because it's an outgoing transaction."
            );
        }

        $walletType = $schema->getProgram() === ZakatDistributionType::NU_CARE_QURBAN
            ? WalletType::QURBAN
            : $schema->getFundingResource();

        $donee = $schema->getDonee() instanceof DoneeContextualRelationSchema
            ? $this->findDonee($schema->getDonee())
            : $this->createDonee($refId, $schema->getDonee());

        if (null === $donee) {
            throw new RepositoryException(
                $this->getRequest(),
                sprintf(
                    'Organization with id \'%s\'not found.',
                    $refId
                )
            );
        }

        $entityManager = $this->getMapper()->getEntityManager();
        $entityManager->getConnection()->beginTransaction();

        $infaqDistribution = new InfaqDistribution();
        $infaqDistribution->setId(Uuid::v7()->toString());

        $infaqDistribution = $this->getMapper()
            ->createReferenced(
                $donee->getId(),
                $schema,
                $infaqDistribution,
                new Donee()
            );

        if (null === $infaqDistribution) {
            $entityManager->getConnection()->rollBack();
            return null;
        }

        $walletSchema = new WalletSchema();
        $walletSchema->setType($walletType);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate($schema->getDate());
        $transactionSchema->setAmount($schema->getAmount());
        $transactionSchema->setType(TransactionType::OUTGOING);
        $transactionSchema->setDescription($schema->getDescription());
        $transactionSchema->setWallet($walletSchema);

        $transactionRepository = new TransactionRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        $transaction = $transactionRepository->create(
            $refId,
            $transactionSchema,
            false
        );

        if (null === $transaction) {
            $entityManager->getConnection()->rollBack();
            return null;
        }

        $transaction->setInfaqDistribution($infaqDistribution);

        $entityManager->flush();
        $entityManager->getConnection()->commit();

        return false === $hydrated
            ? $infaqDistribution
            : MapHydrator::create()->hydrate($infaqDistribution);
    }

    /**
     * @param mixed $refId
     * @param array $schemas
     * @param bool $hydrated
     * @return array
     */
    public function createBulk($refId, array $schemas, bool $hydrated = true): array
    {
        $result = [];

        foreach ($schemas as $key => $schema) {
            try {
                $infaqDistributionSchema = new InfaqDistributionSchema(
                    $schema->getDate(),
                    $schema->getProgram(),
                    $schema->getFundingResource(),
                    $schema->getReceivingCategory(),
                    $schema->getName(),
                    $schema->getAddress(),
                    $schema->getPhoneNumber(),
                    $schema->getNumberOfBeneficiaries(),
                    $schema->getAmount(),
                    $schema->getDescription(),
                    $schema->getProof(),
                    new DoneeContextualRelationSchema($schema->getDoneeId())
                );

                $infaqDistribution = $this->create(
                    $refId,
                    $infaqDistributionSchema,
                    false
                );

                $result[] = $this->createInfaqDistributionStateObject(
                    $key,
                    $infaqDistribution->getId(),
                    HttpCode::CREATED,
                    sprintf(
                        'Infaq distribution created with id \'%s\'.',
                        $infaqDistribution->getId()
                    )
                );
            } catch (Throwable $e) {
                $result[] = $this->createInfaqDistributionStateObject(
                    $key,
                    '',
                    HttpCode::UNPROCESSABLE_ENTITY,
                    $e->getMessage()
                );
            }
        }

        return false === $hydrated
            ? $result
            : ArrayHydrator::create()->hydrate($result);
    }

    /**
     * @param mixed $id
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function update($id, SchemaInterface $schema): EntityInterface|array|null
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->update($id, $schema, new InfaqDistribution());

        return $result;
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function remove($id): EntityInterface|array|null
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->remove($id, new InfaqDistribution());

        return $result;
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|null
     */
    private function createDonee($refId, SchemaInterface $schema): ?EntityInterface
    {
        $donee = new Donee();
        $donee->setId(Uuid::v7()->toString());

        return $this->getMapper()
            ->createReferenced(
                $refId,
                $schema,
                $donee,
                new Organization()
            );
    }

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|null $entity
     */
    private function findDonee(SchemaInterface $schema): ?EntityInterface
    {
        return $this->getMapper()->find(new Donee(), $schema->getId());
    }

    /**
     * @param int $index
     * @param string $id
     * @param int $status
     * @param string $message
     * @return \Schnell\Entity\EntityInterface
     */
    private function createInfaqDistributionStateObject(
        int $index,
        string $id,
        int $status,
        string $message
    ): EntityInterface {
        $state = new InfaqDistributionBulkResponseState();

        $state->setIndex($index);
        $state->setId($id);
        $state->setStatus($status);
        $state->setReason(HttpCode::toString($status));
        $state->setMessage($message);

        return $state;
    }
}
