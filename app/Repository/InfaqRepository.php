<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Amil;
use Lazis\Api\Entity\Infaq;
use Lazis\Api\Entity\Organizer;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Repository\Exception\RepositoryException;
use Lazis\Api\Schema\AmilFundingSchema;
use Lazis\Api\Schema\TransactionSchema;
use Lazis\Api\Schema\WalletSchema;
use Lazis\Api\Type\AmilFunding as AmilFundingType;
use Lazis\Api\Type\InfaqProgram as InfaqProgramType;
use Lazis\Api\Type\Transaction as TransactionType;
use Lazis\Api\Type\Wallet as WalletType;
use Schnell\Entity\EntityInterface;
use Schnell\Hydrator\ArrayHydrator;
use Schnell\Hydrator\MapHydrator;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class InfaqRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new Infaq());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new Infaq());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new Infaq(), $id);

        if (null === $entity) {
            return null;
        }

        return $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function create($refId, SchemaInterface $schema): EntityInterface|array|null
    {
        if ($schema->getAmount() < 0) {
            throw new RepositoryException(
                $this->getRequest(),
                "Infaq transaction amount must be equal or greater than zero. " .
                "Because it's an incoming transaction."
            );
        }

        $amil = $this->getAmilByRefId($refId);

        if (null === $amil) {
            return null;
        }

        $organizer = $this->getOrganizerByAmil($amil);

        if (null === $organizer) {
            return null;
        }

        $organizationRefId = $organizer
            ->getOrganization()
            ->getId();

        $entityManager = $this->getMapper()->getEntityManager();
        $entityManager->getConnection()->beginTransaction();

        $infaq = new Infaq();
        $infaq->setId(Uuid::v7()->toString());

        $infaq = $this->getMapper()
            ->createReferenced(
                $refId,
                $schema,
                $infaq,
                new Amil()
            );

        if (null === $infaq) {
            $entityManager->getConnection()->rollBack();
            return null;
        }

        $walletSchema = new WalletSchema();
        $walletSchema->setType($this->getWalletType($schema->getProgram()));

        $normalizedAmount = $schema->getAmount() - intval($schema->getAmount() * 0.1);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate($schema->getDate());
        $transactionSchema->setAmount($normalizedAmount);
        $transactionSchema->setType(TransactionType::INCOMING);
        $transactionSchema->setDescription($schema->getDescription());
        $transactionSchema->setWallet($walletSchema);

        $transactionRepository = new TransactionRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        $transaction = $transactionRepository->create(
            $organizationRefId,
            $transactionSchema,
            false
        );

        if (null === $transaction) {
            $entityManager->getConnection()->rollBack();
            return null;
        }

        $transaction->setInfaq($infaq);

        $entityManager->flush();
        $entityManager->getConnection()->commit();

        $this->createFundForAmil($refId, $schema);

        return MapHydrator::create()->hydrate($infaq);
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
            ->update($id, $schema, new Infaq());

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
            ->remove($id, new Infaq());

        return $result;
    }

    /**
     * @param mixed $refId
     * @return \Schnell\Entity\EntityInterface|null
     */
    private function getAmilByRefId($refId): ?EntityInterface
    {
        return $this->getMapper()->find(new Amil(), $refId);
    }

    /**
     * @param \Schnell\Entity\EntityInterface $child
     * @return \Schnell\Entity\EntityInterface|null
     */
    private function getOrganizerByAmil(EntityInterface $child): ?EntityInterface
    {
        $super = new Organization();
        $parent = new Organizer();
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $results = $queryBuilder
            ->select([
                $super->getQueryBuilderAlias(),
                $parent->getQueryBuilderAlias()
            ])
            ->from($parent->getDqlName(), $parent->getQueryBuilderAlias())
            ->join(
                $child->getDqlName(),
                $child->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.organizer',
                    $parent->getQueryBuilderAlias(),
                    $child->getQueryBuilderAlias()
                )
            )
            ->join(
                $super->getDqlName(),
                $super->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.organization',
                    $super->getQueryBuilderAlias(),
                    $parent->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $child->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $child->getId())
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (sizeof($results) !== 2) {
            return null;
        }

        $results[0]->setOrganization($results[1]);

        return $results[0];
    }

    /**
     * @param int $program
     * @return int
     */
    private function getWalletType(int $program): int
    {
        return match ($program) {
            InfaqProgramType::NU_CARE_SMART => WalletType::NUCARE_SMART_DISBURSEMENT,
            InfaqProgramType::NU_CARE_EMPOWERED => WalletType::NUCARE_EMPOWERED_DISBURSEMENT,
            InfaqProgramType::NU_CARE_HEALTHY => WalletType::NUCARE_HEALTH_DISBURSEMENT,
            InfaqProgramType::NU_CARE_GREEN => WalletType::NUCARE_GREEN_DISBURSEMENT,
            InfaqProgramType::NU_CARE_PEACE => WalletType::NUCARE_PEACE_DISBURSEMENT,
            InfaqProgramType::CAMPAIGN_PROGRAM => WalletType::CAMPAIGN_PROGRAM,
            InfaqProgramType::DONATION => WalletType::DONATION,
            InfaqProgramType::UNBOUNDED => WalletType::UNBOUNDED_DISBURSEMENT
        };
    }

    /**
     * @param string $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return void
     * @throws \Lazis\Api\Repository\Exception\RepositoryException
     */
    private function createFundForAmil(string $refId, SchemaInterface $schema): void
    {
        $amilFundingSchema = new AmilFundingSchema();
        $amilFundingSchema->setDate($schema->getDate());
        $amilFundingSchema->setFundingType(AmilFundingType::OTHER_AMIL);
        $amilFundingSchema->setName($schema->getName());
        $amilFundingSchema->setAddress($schema->getAddress());
        $amilFundingSchema->setPhoneNumber($schema->getPhoneNumber());
        $amilFundingSchema->setAmount(intval($schema->getAmount() * 0.1));
        $amilFundingSchema->setDescription(
            '(infaq::amil-funding-cut) Pemotongan dari dana Infaq 10%'
        );

        $repository = new AmilFundingRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        $amilFunding = $repository->create(
            $refId,
            $amilFundingSchema
        );

        if (null === $amilFunding) {
            throw new RepositoryException(
                $this->getRequest(),
                sprintf(
                    "(Organization ID: %s): Failed to create amil fund with amount of %d",
                    $refId,
                    $schema->getAmount() * 0.1
                )
            );
        }

        return;
    }
}
