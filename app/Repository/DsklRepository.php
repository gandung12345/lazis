<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Amil;
use Lazis\Api\Entity\Dskl;
use Lazis\Api\Entity\Organizer;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Repository\Exception\RepositoryException;
use Lazis\Api\Schema\AmilFundingSchema;
use Lazis\Api\Schema\TransactionSchema;
use Lazis\Api\Schema\WalletSchema;
use Lazis\Api\Type\AmilFunding as AmilFundingType;
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
class DsklRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new Dskl());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new Dskl());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id): EntityInterface|array|null
    {
        $entity = $this->getMapper()->find(new Dskl(), $id);

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
                "Dskl transaction amount must be equal or greater than zero. " .
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

        $dskl = new Dskl();
        $dskl->setId(Uuid::v7()->toString());

        $dskl = $this->getMapper()
            ->createReferenced(
                $refId,
                $schema,
                $dskl,
                new Amil()
            );

        if (null === $dskl) {
            $entityManager->getConnection()->rollBack();
            return null;
        }

        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::ORGANIZATION_SOCIAL_FUNDING);

        $normalizedAmount = $schema->getAmount() - (intval($schema->getAmount() * 0.1));

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

        $transaction->setDskl($dskl);

        $entityManager->flush();
        $entityManager->getConnection()->commit();

        $this->createFundForAmil($refId, $schema);

        return MapHydrator::create()->hydrate($dskl);
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
            ->update($id, $schema, new Dskl());

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
            ->remove($id, new Dskl());

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
            '(dskl::amil-funding-cut) Pemotongan dari dana DSKL 10%'
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
