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

use function round;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class TwigFromBranchRepresentative implements NuCoinConcreteStrategyInterface
{
    use NuCoinRepositoryStrategyTrait;

    /**
     * @var double
     */
    private const CROSS_TRANSFER_CUT = 0.834;

    /**
     * @var double
     */
    private const NU_COIN_CUT_PERCENTAGE = 0.133;

    /**
     * @var double
     */
    private const RECEIVED_CUT_PERCENTAGE_FOR_AMIL = 0.033;

    /**
     * @var double
     *
     * - 10% masuk ke amil PR (dari 90% dana yang masuk ke PR)
     * - yang ditampilkan ke record hanya 83.333% (dana yang diterima PR)
     */
    private const TWIG_CUT_TO_AMIL_FUND = 0.9;

    /**
     * @var double
     */
    private const AMIL_FUND_FROM_TWIG_CUT = 0.1;

    /**
     * @var double
     */
    private const BRANCH_TO_BREP_TRANSFER_RATIO = 0.9;

    /**
     * {@inheritDoc}
     */
    public function transfer(SchemaInterface $schema): EntityInterface
    {
        $this->checkIfOrganizerAnAmil($schema);

        $nuCoinCrossTransactionRecord = $this->performCrossTransferFromBranchRepresentativeToTwig(
            $schema
        );

        $this->transferAmilFundFromBranchRepresentativeFundReduction($schema);

        return $nuCoinCrossTransactionRecord;
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface &$schema
     * @return \Schnell\Entity\EntityInterface
     */
    private function performCrossTransferFromBranchRepresentativeToTwig(
        SchemaInterface &$schema
    ): EntityInterface {
        $tmp = $schema->getAmount() / 10;
        $dividedFund = $schema->getAmount() / (self::BRANCH_TO_BREP_TRANSFER_RATIO * 10);
        $fundRatio = ((self::BRANCH_TO_BREP_TRANSFER_RATIO + (1.0 - self::BRANCH_TO_BREP_TRANSFER_RATIO)) * 10);
        $parentFund = $dividedFund * $fundRatio;
        $transferredBalance = $parentFund * 0.75;
        $leftoverBalance = $schema->getAmount() - $transferredBalance;

        // - dari 15 juta, 12 juta masuk dompet nu coin mwcnu, 3 juta masuk
        //   dompet amil mwcnu
        // - dari 75 juta, 67.5 masuk dompet nu coin prnu, 7.5 masuk dompet amil
        //   dompet amil prnu

        //$transferredBalance = intval(self::CROSS_TRANSFER_CUT * $schema->getAmount());

        //$realTransferredBalance = intval($transferredBalance * 0.9);
        $twigAmilCut = intval($transferredBalance * 0.1);

        // transfer nu coin balance from branch rep. to twig
        $schema->setAmount(intval($transferredBalance));
        $this->transferCrossOrganization($schema);

        // transfer amil fund to twig wallet from
        // twig nu coin wallet cut.
        $schema->setAmount($twigAmilCut);
        $this->createFundForTwigAmil($schema);

        // set leftover balance after do cross transfer
        $schema->setAmount(intval($leftoverBalance));

        $nuCoinCrossTransactionRecordSchema = new NuCoinCrossTransactionRecordSchema();
        $nuCoinCrossTransactionRecordSchema->setDate($schema->getIssuedAt());
        $nuCoinCrossTransactionRecordSchema->setSourceId($schema->getSourceId());
        $nuCoinCrossTransactionRecordSchema->setSourceName($schema->getSourceName());
        $nuCoinCrossTransactionRecordSchema->setDestinationId($schema->getDestinationId());
        $nuCoinCrossTransactionRecordSchema->setDestinationName($schema->getDestinationName());
        $nuCoinCrossTransactionRecordSchema->setStatus($schema->getStatus());
        $nuCoinCrossTransactionRecordSchema->setType($schema->getType());
        $nuCoinCrossTransactionRecordSchema->setAmount(intval($transferredBalance));
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
    private function transferAmilFundFromBranchRepresentativeFundReduction(
        SchemaInterface $schema
    ): void {
        $amilFund = intval(0.2 * $schema->getAmount());

        // perform amount reduction on branch representative nu coin wallet
        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::NU_COIN);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate($schema->getIssuedAt());
        $transactionSchema->setAmount($amilFund * -1);
        $transactionSchema->setType(TransactionType::OUTGOING);
        $transactionSchema->setDescription(
            sprintf(
                '(dana-amil::pemotongan): Pemotongan dana amil sebesar 2%% dari ' .
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

        // perform addition on source (MWCNU) amil wallet
        $schema->setAmount($amilFund);
        $this->createFundForAmil($schema);
    }

    /**
     * @internal
     *
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return void
     */
    private function createFundForTwigAmil(SchemaInterface $schema): void
    {
        /* experimental: perform amount reduction on twig nu coin wallet */
        $walletSchema = new WalletSchema();
        $walletSchema->setType(WalletType::NU_COIN);

        $transactionSchema = new TransactionSchema();
        $transactionSchema->setDate($schema->getIssuedAt());
        $transactionSchema->setAmount($schema->getAmount() * -1);
        $transactionSchema->setType(TransactionType::OUTGOING);
        $transactionSchema->setDescription(
            '(nu-cross-transaction::twig-fund-cut-for-amil): Pemotongan 10%% untuk ' .
            'dompet amil PRNU.'
        );

        $transactionSchema->setWallet($walletSchema);

        $transactionRepository = new TransactionRepository(
            $this->getMapper(),
            $this->getRequest()
        );

        $transaction = $transactionRepository->create(
            $schema->getDestinationId(),
            $transactionSchema,
            false
        );

        if (null === $transaction) {
            return;
        }

        // perform amount addition on nu coin amil wallet.
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
            '(nu-cross-transaction::amil-funding-transfer): Transfer dari dana nu coin ' .
            'PRNU untuk dompet amil PRNU sebesar 10%%.'
        );

        $amil = $this->getAmilByOrganizerId($schema);
        $repository = new AmilFundingRepository($this->getMapper(), $this->getRequest());

        $amilFunding = $repository->create(
            $amil->getId(),
            $amilFundingSchema,
            $schema->getDestinationId()
        );

        if (null === $amilFunding) {
            throw new RepositoryException(
                $this->getRequest(),
                sprintf(
                    "(Organization ID: %s): Failed to create amil fund with amount of %d.",
                    $schema->getDestinationId(),
                    $schema->getAmount()
                )
            );
        }

        return;
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
                'silang dari MWCNU dengan ID (%s) ke PRNU dengan ID (%s) sebesar 3%%.',
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
                    "(Organization ID: %s): Failed to create amil fund with amount of %d.",
                    $schema->getDestinationId(),
                    $schema->getAmount()
                )
            );
        }

        return;
    }
}
