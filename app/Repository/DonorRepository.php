<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Throwable;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Donor;
use Lazis\Api\Entity\DonorBulkResponseState;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Volunteer;
use Schnell\Entity\EntityInterface;
use Schnell\Hydrator\ArrayHydrator;
use Schnell\Hydrator\MapHydrator;
use Schnell\Http\Code as HttpCode;
use Schnell\Paginator\Paginator;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;
use Symfony\Component\Uid\Uuid;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DonorRepository extends AbstractRepository
{
    use RepositoryTrait;

    /**
     * @return array
     */
    public function paginate(): array
    {
        $count = $this->getMapper()
            ->withRequest($this->getRequest())
            ->count(new Donor());
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withRequest($this->getRequest())
            ->withPage($page)
            ->paginate(new Donor());

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param \Schnell\Entity\EntityInterface $parent
     * @return array
     */
    public function paginateByParent(EntityInterface $parent): array
    {
        $count = $this->getMapper()
            ->countByParent(new Donor(), $parent);
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withHydrator(new ArrayHydrator())
            ->withPage($page)
            ->paginateByParent('volunteer', $parent, new Donor());

        return $result;
    }

    /**
     * @param mixed $id
     * @return Schnell\Entity\EntityInterface|array|null
     */
    public function getById($id): EntityInterface|null|array
    {
        $entity = $this->getMapper()->find(new Donor(), $id);

        if (null === $entity) {
            return null;
        }

        return $this->hydrateEntityWithParent($entity, $this->getRequest());
    }

    /**
     * @param mixed $refId
     * @param Schnell\Schema\SchemaInterface $schema
     * @param bool $hydrated
     * @return Schnell\Entity\EntityInterface|null|array
     */
    public function create(
        $refId,
        SchemaInterface $schema,
        bool $hydrated = true
    ): EntityInterface|null|array {
        $donor = new Donor();
        $donor->setId(Uuid::v7()->toString());

        $entity = $this->getMapper()
            ->withHydrator($hydrated ? new MapHydrator() : null)
            ->createReferenced(
                $refId,
                $schema,
                $donor,
                new Volunteer()
            );

        return $entity;
    }

    /**
     * @param mixed $refId
     * @param \Schnell\Entity\EntityInterface $parent
     * @param array $schemas
     * @param bool $hydrated
     * @return array
     */
    public function createBulk(
        $refId,
        EntityInterface $parent,
        array $schemas,
        bool $hydrated = false
    ): array {
        $result = [];

        foreach ($schemas as $key => $schema) {
            try {
                $donor = $this->create($refId, $schema, false);
                $result[] = $this->createDonorStateObject(
                    $key,
                    $donor->getId(),
                    HttpCode::CREATED,
                    sprintf('Donor created with id \'%s\'.', $donor->getId())
                );
            } catch (Throwable $e) {
                $result[] = $this->createDonorStateObject(
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
     * @param Schnell\Schema\SchemaInterface $schema
     * @return Schnell\Entity\EntityInterface|null|array
     */
    public function update($id, SchemaInterface $schema): EntityInterface|null|array
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->update($id, $schema, new Donor());

        return $result;
    }

    /**
     * @param mixed $id
     * @return Schnell\Entity\EntityInterface|null|array
     */
    public function remove($id): EntityInterface|null|array
    {
        $result = $this->getMapper()
            ->withHydrator(new MapHydrator())
            ->remove($id, new Donor());

        return $result;
    }

    /**
     * @param mixed $refId
     * @param mixed $refType
     * @return int
     */
    public function countByVolunteerIdAndType($refId, $refType): int
    {
        $parent = new Volunteer();
        $entity = new Donor();
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $resultCount = $queryBuilder
            ->select(
                sprintf(
                    'count(%s)',
                    $entity->getQueryBuilderAlias()
                )
            )
            ->from($parent->getDqlName(), $parent->getQueryBuilderAlias())
            ->join(
                $entity->getDqlName(),
                $entity->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.%s',
                    $parent->getQueryBuilderAlias(),
                    $entity->getQueryBuilderAlias(),
                    ltrim(rtrim($parent->getQueryBuilderAlias(), '_'), '_')
                )
            )
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq(
                    sprintf('%s.id', $parent->getQueryBuilderAlias()),
                    '?1'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.type', $parent->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, $refId)
            ->setParameter(2, $refType)
            ->getQuery()
            ->getSingleScalarResult();

        return $resultCount;
    }

    /**
     * @param mixed $refId
     * @param mixed $refType
     * @return array|null
     */
    public function paginateByVolunteerIdAndType($refId, $refType): ?array
    {
        $entity = new Donor();
        $parent = new Volunteer();

        $queryBuilder = $this->getMapper()
            ->getEntityManager()
            ->createQueryBuilder();

        $queryBuilder = $queryBuilder
            ->select($entity->getQueryBuilderAlias())
            ->from($parent->getDqlName(), $parent->getQueryBuilderAlias())
            ->join(
                $entity->getDqlName(),
                $entity->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.%s',
                    $parent->getQueryBuilderAlias(),
                    $entity->getQueryBuilderAlias(),
                    ltrim(rtrim($parent->getQueryBuilderAlias(), '_'), '_')
                )
            )
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq(
                    sprintf('%s.id', $parent->getQueryBuilderAlias()),
                    '?1'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.type', $parent->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, $refId)
            ->setParameter(2, $refType);

        $count = $this->countByVolunteerIdAndType($refId, $refType);
        $paginator = new Paginator($count);
        $page = $paginator->getMetadata($this->getRequest());
        $result = $this->getMapper()
            ->withPage($page)
            ->paginateFromQueryBuilder(
                $queryBuilder,
                $entity
            );

        return $this->hydrateListWithParent($result, $this->getRequest());
    }

    /**
     * @param int $index
     * @param string $id
     * @param int $status
     * @param string $message
     * @return \Schnell\Entity\EntityInterface
     */
    private function createDonorStateObject(
        int $index,
        string $id,
        int $status,
        string $message
    ): EntityInterface {
        $state = new DonorBulkResponseState();

        $state->setIndex($index);
        $state->setId($id);
        $state->setStatus($status);
        $state->setReason(HttpCode::toString($status));
        $state->setMessage($message);

        return $state;
    }

    /**
     * @param string $mRefId
     * @param string $refId
     * @param string $refType
     * @return array
     */
    public function fetchDonorSheetData(string $mRefId, string $refId, string $refType): array
    {
        $nuCoinAggregatorContextualRepository = new NuCoinAggregatorContextualRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        $donors = $this->fetchAllDonorByVolunteerIdAndType($mRefId, $refId, $refType);
        $aggregationDonors = $nuCoinAggregatorContextualRepository->fetchAllDonors($mRefId);

        $filteredAggregationDonors = $donors;

        foreach ($aggregationDonors as $elem) {
            $filteredAggregationDonors = array_filter(
                $filteredAggregationDonors,
                function (EntityInterface $entity) use ($elem) {
                    return $elem->getId() !== $entity->getId();
                }
            );
        }

        return $filteredAggregationDonors;
    }

    /**
     * @param string $mRefId
     * @param string $refId
     * @param string $refType
     * @return array
     */
    public function fetchAllDonorByVolunteerIdAndType(string $mRefId, string $refId, string $refType): array
    {
        $entities = [new Organization(), new Volunteer(), new Donor()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $results = $queryBuilder
            ->select($entities[2]->getQueryBuilderAlias())
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
            ->join(
                $entities[2]->getDqlName(),
                $entities[2]->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.volunteer',
                    $entities[1]->getQueryBuilderAlias(),
                    $entities[2]->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq(
                    sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                    '?1'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.id', $entities[1]->getQueryBuilderAlias()),
                    '?2'
                ),
                $queryBuilder->expr()->eq(
                    sprintf('%s.type', $entities[1]->getQueryBuilderAlias()),
                    '?3'
                )
            ))
            ->setParameter(1, $mRefId)
            ->setParameter(2, $refId)
            ->setParameter(3, $refType)
            ->getQuery()
            ->getResult();

        return $results;
    }
}
