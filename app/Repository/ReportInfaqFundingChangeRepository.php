<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Donee;
use Lazis\Api\Entity\InfaqDistribution;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Transaction;
use Lazis\Api\Entity\Wallet;
use Lazis\Api\Entity\WalletMutation;
use Lazis\Api\Entity\Report\InfaqFundingChangeReport;
use Lazis\Api\Type\Wallet as WalletType;
use Lazis\Api\Type\ZakatDistribution as ZakatDistributionType;
use Schnell\Entity\EntityInterface;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;

use function abs;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ReportInfaqFundingChangeRepository extends AbstractRepository
{
    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     */
    public function generateReport(SchemaInterface $schema): array
    {
        $result = [];

        $this->getBoundedNuCareSmartTotalFund($schema, $result);
        $this->getBoundedNuCareEmpoweredTotalFund($schema, $result);
        $this->getBoundedNuCareHealthTotalFund($schema, $result);
        $this->getBoundedNuCareGreenTotalFund($schema, $result);
        $this->getBoundedNuCarePeaceTotalFund($schema, $result);
        $this->getBoundedDonationFund($schema, $result);
        $this->getUnboundedFund($schema, $result);
        $this->getNuCoinFund($schema, $result);
        $this->aggregateAmilAllocatedFundFromInfaq($schema, $result);

        $this->getDistributionFundByProgramType(
            $schema,
            $result,
            ZakatDistributionType::NU_CARE_SMART
        );

        $this->getDistributionFundByProgramType(
            $schema,
            $result,
            ZakatDistributionType::NU_CARE_EMPOWERED
        );

        $this->getDistributionFundByProgramType(
            $schema,
            $result,
            ZakatDistributionType::NU_CARE_HEALTHY
        );

        $this->getDistributionFundByProgramType(
            $schema,
            $result,
            ZakatDistributionType::NU_CARE_GREEN
        );

        $this->getDistributionFundByProgramType(
            $schema,
            $result,
            ZakatDistributionType::NU_CARE_PEACE
        );

        $this->getDistributionFundByProgramType(
            $schema,
            $result,
            ZakatDistributionType::NU_CARE_QURBAN
        );

        return $result;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @param int $walletType
     * @return void
     */
    private function getTotalFundByWalletType(
        SchemaInterface $schema,
        array &$results,
        int $walletType
    ): void {
        $entities = [new Organization(), new WalletMutation()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $rlists = $queryBuilder
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
                $queryBuilder->expr()->gte(
                    sprintf('%s.year', $entities[1]->getQueryBuilderAlias()),
                    '?3'
                ),
                $queryBuilder->expr()->lte(
                    sprintf('%s.year', $entities[1]->getQueryBuilderAlias()),
                    '?4'
                )
            ))
            ->orderBy(
                sprintf('%s.year', $entities[1]->getQueryBuilderAlias()),
                'ASC'
            )
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, $walletType)
            ->setParameter(3, $schema->getYear()->getStart())
            ->setParameter(4, $schema->getYear()->getEnd())
            ->getQuery()
            ->getResult();

        if (sizeof($rlists) === 0) {
            return;
        }

        foreach ($rlists as $rlist) {
            $report = null;

            foreach ($results as $key => &$result) {
                if ($result->getYear() === $rlist->getYear()) {
                    $report = $result;
                    break;
                }
            }

            if ($report === null) {
                $report = new InfaqFundingChangeReport();
                $report->setYear($rlist->getYear());

                $ephemeral = $report->getInfaqReception();

                $this->callReceptionMutatorByWalletType(
                    $ephemeral,
                    $rlist,
                    $walletType
                );

                $results[] = $report;
                continue;
            }

            $ephemeral = $report->getInfaqReception();

            $this->callReceptionMutatorByWalletType(
                $ephemeral,
                $rlist,
                $walletType
            );
        }

        return;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @param int $programType
     * @return void
     */
    private function getDistributionFundByProgramType(
        SchemaInterface $schema,
        array &$results,
        int $programType
    ): void {
        $walletType = $this->getWalletTypeByProgramType($programType);
        $entities = [new Organization(), new Donee(), new InfaqDistribution()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $rlists = $queryBuilder
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
                    '%s.id = %s.donee',
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
                    sprintf('%s.fundingResource', $entities[2]->getQueryBuilderAlias()),
                    '?2'
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->setParameter(2, $walletType)
            ->getQuery()
            ->getResult();

        if (sizeof($rlists) === 0) {
            return;
        }

        foreach ($rlists as $rlist) {
            $year = $rlist->getDate()
                ->withFormat('Y')
                ->stringify();

            if (
                intval($year) < $schema->getYear()->getStart() ||
                intval($year) > $schema->getYear()->getEnd()
            ) {
                continue;
            }

            $report = null;

            foreach ($results as &$result) {
                if ($result->getYear() === intval($year)) {
                    $report = $result;
                    break;
                }
            }

            if (null === $report) {
                $report = new InfaqFundingChangeReport();
                $report->setYear(intval($year));

                $ephemeral = $report->getInfaqDistribution();

                $this->callDistributionMutatorByProgramType(
                    $ephemeral,
                    $rlist,
                    $programType
                );

                $results[] = $report;
                continue;
            }

            $ephemeral = $report->getInfaqDistribution();

            $this->callDistributionMutatorByProgramType(
                $ephemeral,
                $rlist,
                $programType
            );
        }

        return;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function getBoundedNuCareSmartTotalFund(
        SchemaInterface $schema,
        array &$results
    ): void {
        $this->getTotalFundByWalletType(
            $schema,
            $results,
            WalletType::NUCARE_SMART_DISBURSEMENT
        );
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function getBoundedNuCareEmpoweredTotalFund(
        SchemaInterface $schema,
        array &$results
    ): void {
        $this->getTotalFundByWalletType(
            $schema,
            $results,
            WalletType::NUCARE_EMPOWERED_DISBURSEMENT
        );
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function getBoundedNuCareHealthTotalFund(
        SchemaInterface $schema,
        array &$results
    ): void {
        $this->getTotalFundByWalletType(
            $schema,
            $results,
            WalletType::NUCARE_HEALTH_DISBURSEMENT
        );
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function getBoundedNuCareGreenTotalFund(
        SchemaInterface $schema,
        array &$results
    ): void {
        $this->getTotalFundByWalletType(
            $schema,
            $results,
            WalletType::NUCARE_GREEN_DISBURSEMENT
        );
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function getBoundedNuCarePeaceTotalFund(
        SchemaInterface $schema,
        array &$results
    ): void {
        $this->getTotalFundByWalletType(
            $schema,
            $results,
            WalletType::NUCARE_PEACE_DISBURSEMENT
        );
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function getBoundedDonationFund(
        SchemaInterface $schema,
        array &$results
    ): void {
        $this->getTotalFundByWalletType(
            $schema,
            $results,
            WalletType::DONATION
        );
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function getUnboundedFund(
        SchemaInterface $schema,
        array &$results
    ): void {
        $this->getTotalFundByWalletType(
            $schema,
            $results,
            WalletType::UNBOUNDED_DISBURSEMENT
        );
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function getNuCoinFund(SchemaInterface $schema, array &$results): void
    {
        $this->getTotalFundByWalletType($schema, $results, WalletType::NU_COIN);
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array &$results
     * @return void
     */
    private function aggregateAmilAllocatedFundFromInfaq(
        SchemaInterface $schema,
        array &$results
    ): void {
        $entities = [new Organization(), new Wallet(), new Transaction()];
        $entityManager = $this->getMapper()->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $rlists = $queryBuilder
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
                    '%s.id = %s.wallet',
                    $entities[1]->getQueryBuilderAlias(),
                    $entities[2]->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq(
                    sprintf('%s.id', $entities[0]->getQueryBuilderAlias()),
                    '?1'
                ),
                $queryBuilder->expr()->like(
                    sprintf('%s.description', $entities[2]->getQueryBuilderAlias()),
                    $queryBuilder->expr()->literal('(infaq::amil-funding-cut)%')
                )
            ))
            ->setParameter(1, $schema->getOrganizationId())
            ->getQuery()
            ->getResult();

        if (sizeof($rlists) === 0) {
            return;
        }

        foreach ($rlists as $rlist) {
            $year = $rlist->getDate()
                ->withFormat('Y')
                ->stringify();

            if (
                intval($year) < $schema->getYear()->getStart() ||
                intval($year) > $schema->getYear()->getEnd()
            ) {
                continue;
            }

            $report = null;

            foreach ($results as &$result) {
                if ($result->getYear() === intval($year)) {
                    $report = $result;
                    break;
                }
            }

            if ($report === null) {
                $report = new InfaqFundingChangeReport();
                $report->setYear(intval($year));
                $report->getInfaqDistribution()
                    ->addAmilAllocationFund($rlist->getAmount());
                $results[] = $report;
                continue;
            }

            $report->getInfaqDistribution()
                ->addAmilAllocationFund($rlist->getAmount());
        }

        return;
    }

    /**
     * @internal
     *
     * @param int $walletType
     * @return string
     */
    private function getReceptionMutatorByWalletType(int $walletType): string
    {
        return match ($walletType) {
            WalletType::NUCARE_SMART_DISBURSEMENT => 'addBoundedFund',
            WalletType::NUCARE_EMPOWERED_DISBURSEMENT => 'addBoundedFund',
            WalletType::NUCARE_HEALTH_DISBURSEMENT => 'addBoundedFund',
            WalletType::NUCARE_GREEN_DISBURSEMENT => 'addBoundedFund',
            WalletType::NUCARE_PEACE_DISBURSEMENT => 'addBoundedFund',
            WalletType::DONATION => 'addBoundedFund',
            WalletType::UNBOUNDED_DISBURSEMENT => 'addUnboundedFund',
            WalletType::NU_COIN => 'addNuCoinFund'
        };
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\EntityInterface &$report
     * @param \Schnell\Entity\EntityInterface $schema
     * @param int $walletType
     * @return void
     */
    private function callReceptionMutatorByWalletType(
        EntityInterface &$report,
        EntityInterface $entity,
        int $walletType
    ): void {
        call_user_func(
            [$report, $this->getReceptionMutatorByWalletType($walletType)],
            $entity->getAmount()
        );
    }

    /**
     * @internal
     *
     * @param int $programType
     * @return string
     */
    private function getDistributionMutatorByProgramType(int $programType): string
    {
        return match ($programType) {
            ZakatDistributionType::NU_CARE_SMART => 'addNuSmartFund',
            ZakatDistributionType::NU_CARE_EMPOWERED => 'addNuEmpoweredFund',
            ZakatDistributionType::NU_CARE_HEALTHY => 'addNuHealthFund',
            ZakatDistributionType::NU_CARE_GREEN => 'addNuGreenFund',
            ZakatDistributionType::NU_CARE_PEACE => 'addNuPeaceFund'
        };
    }

    /**
     * @internal
     *
     * @param \Schnell\Entity\EntityInterface &$report
     * @param \Schnell\Entity\EntityInterface $entity
     * @param int $programType
     * @return void
     */
    private function callDistributionMutatorByProgramType(
        EntityInterface &$report,
        EntityInterface $entity,
        int $programType
    ): void {
        call_user_func(
            [$report, $this->getDistributionMutatorByProgramType($programType)],
            abs($entity->getAmount())
        );
    }

    /**
     * @internal
     *
     * @param int $programType
     * @return int
     */
    private function getWalletTypeByProgramType(int $programType): int
    {
        return match ($programType) {
            ZakatDistributionType::NU_CARE_SMART => WalletType::NUCARE_SMART_DISBURSEMENT,
            ZakatDistributionType::NU_CARE_EMPOWERED => WalletType::NUCARE_EMPOWERED_DISBURSEMENT,
            ZakatDistributionType::NU_CARE_HEALTHY => WalletType::NUCARE_HEALTH_DISBURSEMENT,
            ZakatDistributionType::NU_CARE_GREEN => WalletType::NUCARE_GREEN_DISBURSEMENT,
            ZakatDistributionType::NU_CARE_PEACE => WalletType::NUCARE_PEACE_DISBURSEMENT,
            ZakatDistributionType::NU_CARE_QURBAN => WalletType::QURBAN
        };
    }
}
