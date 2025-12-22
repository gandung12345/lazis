<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Wallet;
use Schnell\Entity\EntityInterface;
use Schnell\Hydrator\ArrayHydrator;
use Schnell\Hydrator\MapHydrator;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class WalletRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new Wallet());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withPage($page)
            ->withRequest($this->getRequest())
            ->paginate(new Wallet());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param mixed $id
     * @param bool $hydrated
     * @return \Schnell\Entity\EntityInterface|null|array
     */
    public function getById(
        $id,
        bool $hydrated = true
    ): EntityInterface|null|array {
        $wallet = $this->getMapper() ->find(new Wallet(), $id);

        if (null === $wallet) {
            return null;
        }

        return $hydrated === false
            ? $wallet
            : $this->hydrateEntityWithParent($wallet, $this->getRequest());
    }

    /**
     * @param \Schnell\Entity\EntityInterface $parent
     * @param string $id
     * @param bool $hydrated
     * @return \Schnell\Entity\EntityInterface|array|null
     */
    public function findOneWithParent(
        EntityInterface $parent,
        string $id,
        bool $hydrated = false
    ): EntityInterface|array|null {
        $entity = new Wallet();
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $results = $queryBuilder
            ->select($entity->getQueryBuilderAlias())
            ->from($parent->getDqlName(), $parent->getQueryBuilderAlias())
            ->join(
                $entity->getDqlName(),
                $entity->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.organization',
                    $parent->getQueryBuilderAlias(),
                    $entity->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq(
                    sprintf('%s.id', $parent->getQueryBuilderAlias()),
                    '?1'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.id', $entity->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, $parent->getId())
            ->setParameter(2, $id)
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (sizeof($results) !== 1) {
            return null;
        }

        return $hydrated === false
            ? $results[0]
            : MapHydrator::create()->hydrate($results[0]);
    }
}
