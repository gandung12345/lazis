<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Schema\Json;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'transaction')]
#[OpenApi\Schema]
class Transaction extends AbstractEntity
{
    /**
     * @var string
     */
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Transaction ID',
        readOnly: true
    )]
    private string $id;

    /**
     * @var \Schnell\Decorator\Stringified\DateTimeDecorator
     */
    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'date')]
    #[OpenApi\Property(
        property: 'date',
        type: 'string',
        format: 'date',
        description: 'Transaction date'
    )]
    private DateTimeDecorator $date;

    /**
     * @var int
     */
    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'amount')]
    #[OpenApi\Property(
        property: 'amount',
        type: 'integer',
        description: 'Transaction amount'
    )]
    private int $amount;

    /**
     * @var int
     */
    #[Column(type: 'integer', nullable: false)]
    #[Json(name: 'type')]
    #[OpenApi\Property(
        property: 'type',
        type: 'integer',
        description: 'Transaction type'
    )]
    // picklist: Transaction
    private int $type;

    /**
     * @var string
     */
    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'description')]
    #[OpenApi\Property(
        property: 'description',
        type: 'string',
        description: 'Transaction description'
    )]
    private string $description;

    /**
     * @var \Lazis\Api\Entity\Wallet
     */
    #[ManyToOne(
        targetEntity: Wallet::class,
        inversedBy: 'transactions',
        cascade: ['persist']
    )]
    #[JoinColumn(name: 'walletRefId', referencedColumnName: 'id')]
    private ?Wallet $wallet = null;

    #[OneToOne(
        targetEntity: AmilFunding::class,
        inversedBy: 'transaction',
        cascade: ['persist']
    )]
    #[JoinColumn(name: 'amilFundingRefId', referencedColumnName: 'id')]
    private ?AmilFunding $amilFunding = null;

    #[OneToOne(targetEntity: Dskl::class, inversedBy: 'transaction')]
    #[JoinColumn(name: 'dsklRefId', referencedColumnName: 'id')]
    private ?Dskl $dskl = null;

    #[OneToOne(targetEntity: Infaq::class, inversedBy: 'transaction')]
    #[JoinColumn(name: 'infaqRefId', referencedColumnName: 'id')]
    private ?Infaq $infaq = null;

    #[OneToOne(targetEntity: NuCoin::class, inversedBy: 'transaction')]
    #[JoinColumn(name: 'nuCoinRefId', referencedColumnName: 'id')]
    private ?NuCoin $nuCoin = null;

    #[OneToOne(targetEntity: NuCoinAggregator::class, inversedBy: 'transaction')]
    #[JoinColumn(name: 'nuCoinAggregatorRefId', referencedColumnName: 'id')]
    private ?NuCoinAggregator $nuCoinAggregator = null;

    #[OneToOne(targetEntity: Zakat::class, inversedBy: 'transaction')]
    #[JoinColumn(name: 'zakatRefId', referencedColumnName: 'id')]
    private ?Zakat $zakat = null;

    #[OneToOne(targetEntity: ZakatDistribution::class, inversedBy: 'transaction')]
    #[JoinColumn(name: 'zakatDistributionRefId', referencedColumnName: 'id')]
    private ?ZakatDistribution $zakatDistribution = null;

    #[OneToOne(targetEntity: NonHalalFundingReceive::class, inversedBy: 'transaction')]
    #[JoinColumn(name: 'nonHalalFundingReceiveRefId', referencedColumnName: 'id')]
    private ?NonHalalFundingReceive $nonHalalFundingReceive = null;

    #[OneToOne(
        targetEntity: NonHalalFundingDistribution::class,
        inversedBy: 'transaction'
    )]
    #[JoinColumn(name: 'nonHalalFundingDistributionRefId', referencedColumnName: 'id')]
    private ?NonHalalFundingDistribution $nonHalalFundingDistribution = null;

    #[OneToOne(targetEntity: InfaqDistribution::class, inversedBy: 'transaction')]
    #[JoinColumn(name: 'infaqDistributionRefId', referencedColumnName: 'id')]
    private ?InfaqDistribution $infaqDistribution = null;

    #[OneToOne(targetEntity: AmilFundingUsage::class, inversedBy: 'transaction')]
    #[JoinColumn(name: 'amilFundingUsageRefId', referencedColumnName: 'id')]
    private ?AmilFundingUsage $amilFundingUsage = null;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return void
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return \Schnell\Decorator\Stringified\DateTimeDecorator
     */
    public function getDate(): DateTimeDecorator
    {
        return $this->date;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator $date
     * @return void
     */
    public function setDate(DateTimeDecorator $date): void
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     * @return void
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return void
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return \Lazis\Api\Entity\Wallet|null
     */
    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    /**
     * @param \Lazis\Api\Entity\Wallet|null $wallet
     * @return void
     */
    public function setWallet(?Wallet $wallet): void
    {
        $this->wallet = $wallet;
    }

    /**
     * @return \Lazis\Api\Entity\AmilFunding|null
     */
    public function getAmilFunding(): ?AmilFunding
    {
        return $this->amilFunding;
    }

    /**
     * @param \Lazis\Api\Entity\AmilFunding|null $amilFunding
     * @return void
     */
    public function setAmilFunding(?AmilFunding $amilFunding): void
    {
        $this->amilFunding = $amilFunding;
    }

    /**
     * @return \Lazis\Api\Entity\Dskl|null
     */
    public function getDskl(): ?Dskl
    {
        return $this->dskl;
    }

    /**
     * @param \Lazis\Api\Entity\Dskl|null $dskl
     * @return void
     */
    public function setDskl(?Dskl $dskl): void
    {
        $this->dskl = $dskl;
    }

    /**
     * @return \Lazis\Api\Entity\Infaq|null
     */
    public function getInfaq(): ?Infaq
    {
        return $this->infaq;
    }

    /**
     * @param \Lazis\Api\Entity\Infaq|null $infaq
     * @return void
     */
    public function setInfaq(?Infaq $infaq): void
    {
        $this->infaq = $infaq;
    }

    /**
     * @return \Lazis\Api\Entity\NuCoin|null
     */
    public function getNuCoin(): ?NuCoin
    {
        return $this->nuCoin;
    }

    /**
     * @param \Lazis\Api\Entity\NuCoin|null $nuCoin
     * @return void
     */
    public function setNuCoin(?NuCoin $nuCoin): void
    {
        $this->nuCoin = $nuCoin;
    }

    /**
     * @return \Lazis\Api\Entity\NuCoinAggregator|null
     */
    public function getNuCoinAggregator(): ?NuCoinAggregator
    {
        return $this->nuCoinAggregator;
    }

    /**
     * @param \Lazis\Api\Entity\NuCoinAggregator|null $nuCoinAggregator
     * @return void
     */
    public function setNuCoinAggregator(?NuCoinAggregator $nuCoinAggregator): void
    {
        $this->nuCoinAggregator = $nuCoinAggregator;
    }

    /**
     * @return \Lazis\Api\Entity\Zakat|null
     */
    public function getZakat(): ?Zakat
    {
        return $this->zakat;
    }

    /**
     * @param \Lazis\Api\Entity\Zakat|null $zakat
     * @return void
     */
    public function setZakat(?Zakat $zakat): void
    {
        $this->zakat = $zakat;
    }

    /**
     * @return \Lazis\Api\Entity\ZakatDistribution|null
     */
    public function getZakatDistribution(): ?ZakatDistribution
    {
        return $this->zakatDistribution;
    }

    /**
     * @param \Lazis\Api\Entity\ZakatDistribution|null $zakatDistribution
     * @return void
     */
    public function setZakatDistribution(?ZakatDistribution $zakatDistribution): void
    {
        $this->zakatDistribution = $zakatDistribution;
    }

    /**
     * @return \Lazis\Api\Entity\NonHalalFundingReceive|null
     */
    public function getNonHalalFundingReceive(): ?NonHalalFundingReceive
    {
        return $this->nonHalalFundingReceive;
    }

    /**
     * @param \Lazis\Api\Entity\NonHalalFundingReceive|null $nonHalalFundingReceive
     * @return void
     */
    public function setNonHalalFundingReceive(
        ?NonHalalFundingReceive $nonHalalFundingReceive
    ): void {
        $this->nonHalalFundingReceive = $nonHalalFundingReceive;
    }

    /**
     * @return \Lazis\Api\Entity\NonHalalFundingDistribution|null
     */
    public function getNonHalalFundingDistribution(): ?NonHalalFundingDistribution
    {
        return $this->nonHalalFundingDistribution;
    }

    /**
     * @param \Lazis\Api\Entity\NonHalalFundingDistribution|null $nonHalalFundingDistribution
     * @return void
     */
    public function setNonHalalFundingDistribution(
        ?NonHalalFundingDistribution $nonHalalFundingDistribution
    ): void {
        $this->nonHalalFundingDistribution = $nonHalalFundingDistribution;
    }

    /**
     * @return \Lazis\Api\Entity\InfaqDistribution|null
     */
    public function getInfaqDistribution(): ?InfaqDistribution
    {
        return $this->infaqDistribution;
    }

    /**
     * @param \Lazis\Api\Entity\InfaqDistribution|null $infaqDistribution
     * @return void
     */
    public function setInfaqDistribution(?InfaqDistribution $infaqDistribution): void
    {
        $this->infaqDistribution = $infaqDistribution;
    }

    /**
     * @return \Lazis\Api\Entity\AmilFundingUsage|null
     */
    public function getAmilFundingUsage(): ?AmilFundingUsage
    {
        return $this->amilFundingUsage;
    }

    /**
     * @param \Lazis\Api\Entity\AmilFundingUsage|null
     * @return void
     */
    public function setAmilFundingUsage(?AmilFundingUsage $amilFundingUsage): void
    {
        $this->amilFundingUsage = $amilFundingUsage;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__transaction__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'transaction';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return Transaction::class;
    }
}
