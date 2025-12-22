<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\WalletMutation;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\EntityInterface;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class WalletMutationRepository extends AbstractRepository
{
    /**
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|null
     */
    public function create($refId, SchemaInterface $schema): EntityInterface|null
    {
        $walletMutation = $this->findWalletMutation($refId, $schema);

        if (null === $walletMutation) {
            return $this->createWalletMutation($refId, $schema);
        }

        return $this->updateWalletMutation($walletMutation, $schema);
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|null
     */
    private function createWalletMutation($refId, SchemaInterface $schema): EntityInterface|null
    {
        $walletMutation = new WalletMutation();
        $walletMutation->setId(Uuid::v7()->toString());

        $entity = $this->getMapper()
            ->withHydrator(null)
            ->createReferenced(
                $refId,
                $schema,
                $walletMutation,
                new Organization()
            );

        return $entity;
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return \Schnell\Entity\EntityInterface|null
     */
    private function findWalletMutation($refId, SchemaInterface $schema): EntityInterface|null
    {
        $entities = [new Organization(), new WalletMutation()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $results = $queryBuilder
            ->select($entities[1]->getQueryBuilderAlias())
            ->from($entities[0]->getDqlName(), $entities[0]->getQueryBuilderAlias())
            ->join(
                $entities[1]->getDqlName(),
                $entities[1]->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.organization',
                    $entities[0]->getQueryBuilderAlias(),
                    $entities[1]->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq(
                    sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                    '?1'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.type', $entities[1]->getQueryBuilderAlias()),
                    '?2'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.year', $entities[1]->getQueryBuilderAlias()),
                    '?3'
                )
            ))
            ->setParameter(1, $refId)
            ->setParameter(2, $schema->getType())
            ->setParameter(3, $schema->getYear())
            ->getQuery()
            ->getResult();

        if (sizeof($results) === 0) {
            return null;
        }

        return $results[0];
    }

    /**
     * @param \Schnell\Entity\EntityInterface $entity
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return void
     */
    private function updateWalletMutation(
        EntityInterface $entity,
        SchemaInterface $schema
    ): EntityInterface {
        $entityManager = $this->getMapper()->getEntityManager();

        if ($entityManager->getConnection()->isTransactionActive()) {
            $entityManager->lock($entity, LockMode::PESSIMISTIC_WRITE);
        }

        $entity->setAmount($entity->getAmount() + $schema->getAmount());
        $entityManager->flush();

        return $entity;
    }
}
