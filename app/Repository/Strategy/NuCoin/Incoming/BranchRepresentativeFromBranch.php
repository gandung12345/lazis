<?php

declare(strict_types=1);

namespace Lazis\Api\Repository\Strategy\NuCoin\Incoming;

use Lazis\Api\Repository\AmilFundingRepository;
use Lazis\Api\Repository\NuCoinCrossTransactionRecordRepository;
use Lazis\Api\Repository\OrganizerRepository;
use Lazis\Api\Repository\TransactionRepository;
use Lazis\Api\Repository\Exception\RepositoryException;
use Lazis\Api\Repository\Strategy\NuCoin\NuCoinConcreteStrategyInterface;
use Lazis\Api\Repository\Strategy\NuCoin\NuCoinRepositoryStrategyTrait;
use Lazis\Api\Schema\AmilFundingSchema;
use Lazis\Api\Schema\NuCoinCrossTransactionRecordSchema;
use Lazis\Api\Schema\TransactionSchema;
use Lazis\Api\Schema\WalletSchema;
use Lazis\Api\Type\AmilFunding as AmilFundingType;
use Lazis\Api\Type\Transaction as TransactionType;
use Lazis\Api\Type\Wallet as WalletType;
use Schnell\Entity\EntityInterface;
use Schnell\Schema\SchemaInterface;

use function intval;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class BranchRepresentativeFromBranch implements NuCoinConcreteStrategyInterface
{
    use NuCoinRepositoryStrategyTrait;

    /**
     * @var double
     */
    private const CROSS_TRANSFER_CUT = 0.9;

    /**
     * @var double
     */
    private const NU_COIN_CUT_PERCENTAGE = 0.065;

    /**
     * @var double
     */
    private const RECEIVED_CUT_PERCENTAGE_FOR_AMIL = 0.035;

    /**
     * {@inheritDoc}
     */
    public function transfer(SchemaInterface $schema): EntityInterface
    {
        // calculate nu coin fund (6.5%) for source organization id (branch)
        // calculate amil fund (3.5%) for source organization id (branch)
        // put 90% of transfer fund to destination organization id (branch representative)
        $this->checkIfOrganizerAnAmil($schema);

        // transfer 90% of the fund to destination
        $nuCoinCrossTransactionRecord = $this->performCrossTransferFromBranchToBranchRepresentative(
            $schema
        );

        $this->transferAmilFundFromBranchFundReduction($schema);

        return $nuCoinCrossTransactionRecord;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface &$schema
     * @return \Schnell\Entity\EntityInterface
     */
    private function performCrossTransferFromBranchToBranchRepresentative(
        SchemaInterface &$schema
    ): EntityInterface {
        $transferredBalance = intval(self::CROSS_TRANSFER_CUT * $schema->getAmount());

        $leftoverPercentage = self::NU_COIN_CUT_PERCENTAGE + self::RECEIVED_CUT_PERCENTAGE_FOR_AMIL;
        $leftoverBalance = intval($leftoverPercentage * $schema->getAmount());

        $schema->setAmount($transferredBalance);
        $this->transferCrossOrganization($schema, true);

        // set leftover balance after do cross transfer
        $schema->setAmount($leftoverBalance);

        $nuCoinCrossTransactionRecordSchema = new NuCoinCrossTransactionRecordSchema();
        $nuCoinCrossTransactionRecordSchema->setDate($schema->getIssuedAt());
        $nuCoinCrossTransactionRecordSchema->setSourceId($schema->getSourceId());
        $nuCoinCrossTransactionRecordSchema->setSourceName($schema->getSourceName());
        $nuCoinCrossTransactionRecordSchema->setDestinationId($schema->getDestinationId());
        $nuCoinCrossTransactionRecordSchema->setDestinationName($schema->getDestinationName());
        $nuCoinCrossTransactionRecordSchema->setStatus($schema->getStatus());
        $nuCoinCrossTransactionRecordSchema->setType($schema->getType());
        $nuCoinCrossTransactionRecordSchema->setAmount($transferredBalance);
        $nuCoinCrossTransactionRecordSchema->setProof($schema->getProof());

        $nuCoinCrossTransactionRecordRepository = new NuCoinCrossTransactionRecordRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        return $nuCoinCrossTransactionRecordRepository->create(
            $nuCoinCrossTransactionRecordSchema,
            false
        );
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return void
     */
    private function transferAmilFundFromBranchFundReduction(SchemaInterface $schema): void
    {
        $amilFundPercentage = self::RECEIVED_CUT_PERCENTAGE_FOR_AMIL * 10;
        $amilFund = intval($amilFundPercentage * $schema->getAmount());

        // perform amount reduction on branch nu coin wallet
        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::NU_COIN);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate($schema->getIssuedAt());
        $transactionSchema->setAmount($amilFund * -1);
        $transactionSchema->setType(TransactionType::OUTGOING);
        $transactionSchema->setDescription(
            sprintf(
                '(dana-amil::pemotongan): Pemotongan dana amil sebesar 3.5%% dari ' .
                'dompet NU coin dari ID organisasi (%s)',
                $schema->getSourceId()
            )
        );
        $transactionSchema->setWallet($walletSchema);

        $transactionRepository = new TransactionRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        $transaction = $transactionRepository->create(
            $schema->getSourceId(),
            $transactionSchema,
            false
        );

        // perform addition on source amil wallet
        $schema->setAmount($amilFund);
        $this->createFundForAmil($schema);
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return void
     */
    private function createFundForAmil(SchemaInterface $schema): void
    {
        $organizerRepository = new OrganizerRepository($this->getMapper(), $this->getRequest());
        $organizer = $organizerRepository->getById($schema->getOrganizerId(), false);

        if (null === $organizer) {
            throw new RepositoryException(
                $this->getRequest(),
                sprintf("Organizer data with id '%s' not found.", $schema->getOrganizerId())
            );
        }

        $amilFundingSchema = new AmilFundingSchema();
        $amilFundingSchema->setDate($schema->getIssuedAt());
        $amilFundingSchema->setFundingType(AmilFundingType::OTHER_AMIL);
        $amilFundingSchema->setName($organizer->getName());
        $amilFundingSchema->setAddress($organizer->getAddress());
        $amilFundingSchema->setPhoneNumber($organizer->getPhoneNumber());
        $amilFundingSchema->setAmount($schema->getAmount());
        $amilFundingSchema->setDescription(
            sprintf(
                '(nu-cross-transaction::amil-funding-cut): Pemotongan dari dana transfer ' .
                'silang dari PCNU dengan ID (%s) ke MWCNU dengan ID (%s) sebesar 3.5%%.',
                $schema->getSourceId(),
                $schema->getDestinationId()
            )
        );

        $amil = $this->getAmilByOrganizerId($schema);
        $repository = new AmilFundingRepository($this->getMapper(), $this->getRequest());
        $amilFunding = $repository->create(
            $amil->getId(),
            $amilFundingSchema,
            $schema->getSourceId()
        );

        if (null === $amilFunding) {
            throw new RepositoryException(
                $this->getRequest(),
                sprintf(
                    '(Organization ID: %s): Failed to create amil fund with amount of %d.',
                    $schema->getDestinationId(),
                    $schema->getAmount()
                )
            );
        }

        return;
    }
}
