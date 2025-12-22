<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report\Components;

use RuntimeException;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class AmilFundingReception extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'amilFundFromZakat')]
    private int $amilFundFromZakat;

    /**
     * @var int
     */
    #[Json(name: 'amilFundFromInfaq')]
    private int $amilFundFromInfaq;

    /**
     * @var int
     */
    #[Json(name: 'amilFundFromDskl')]
    private int $amilFundFromDskl;

    /**
     * @var int
     */
    #[Json(name: 'amilFundFromOther')]
    private int $amilFundFromOther;

    /**
     * @var int
     */
    #[Json(name: 'amilFundFromGrant')]
    private int $amilFundFromGrant;

    public function __construct()
    {
        $this->setAmilFundFromZakat(0);
        $this->setAmilFundFromInfaq(0);
        $this->setAmilFundFromDskl(0);
        $this->setAmilFundFromOther(0);
        $this->setAmilFundFromGrant(0);
    }

    /**
     * @return int
     */
    public function getAmilFundFromZakat(): int
    {
        return $this->amilFundFromZakat;
    }

    /**
     * @param int $amilFundFromZakat
     * @return void
     */
    public function setAmilFundFromZakat(int $amilFundFromZakat): void
    {
        $this->amilFundFromZakat = $amilFundFromZakat;
    }

    /**
     * @param int $amilFundFromZakat
     * @return void
     */
    public function addAmilFundFromZakat(int $amilFundFromZakat): void
    {
        $this->amilFundFromZakat += $amilFundFromZakat;
    }

    /**
     * @return int
     */
    public function getAmilFundFromInfaq(): int
    {
        return $this->amilFundFromInfaq;
    }

    /**
     * @param int $amilFundFromInfaq
     * @return void
     */
    public function setAmilFundFromInfaq(int $amilFundFromInfaq): void
    {
        $this->amilFundFromInfaq = $amilFundFromInfaq;
    }

    /**
     * @param int $amilFundFromInfaq
     * @return void
     */
    public function addAmilFundFromInfaq(int $amilFundFromInfaq): void
    {
        $this->amilFundFromInfaq += $amilFundFromInfaq;
    }

    /**
     * @return int
     */
    public function getAmilFundFromDskl(): int
    {
        return $this->amilFundFromDskl;
    }

    /**
     * @param int $amilFundFromDskl
     * @return void
     */
    public function setAmilFundFromDskl(int $amilFundFromDskl): void
    {
        $this->amilFundFromDskl = $amilFundFromDskl;
    }

    /**
     * @param int $amilFundFromDskl
     * @return void
     */
    public function addAmilFundFromDskl(int $amilFundFromDskl): void
    {
        $this->amilFundFromDskl += $amilFundFromDskl;
    }

    /**
     * @return int
     */
    public function getAmilFundFromOther(): int
    {
        return $this->amilFundFromOther;
    }

    /**
     * @param int $amilFundFromOther
     * @return void
     */
    public function setAmilFundFromOther(int $amilFundFromOther): void
    {
        $this->amilFundFromOther = $amilFundFromOther;
    }

    /**
     * @param int $amilFundFromOther
     * @return void
     */
    public function addAmilFundFromOther(int $amilFundFromOther): void
    {
        $this->amilFundFromOther += $amilFundFromOther;
    }

    /**
     * @return int
     */
    public function getAmilFundFromGrant(): int
    {
        return $this->amilFundFromGrant;
    }

    /**
     * @param int $amilFundFromGrant
     * @return void
     */
    public function setAmilFundFromGrant(int $amilFundFromGrant): void
    {
        $this->amilFundFromGrant = $amilFundFromGrant;
    }

    /**
     * @param int $amilFundFromGrant
     * @return void
     */
    public function addAmilFundFromGrant(int $amilFundFromGrant): void
    {
        $this->amilFundFromGrant += $amilFundFromGrant;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        throw new RuntimeException("Not implemented.");
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        throw new RuntimeException("Not implemented.");
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        throw new RuntimeException("Not implemented.");
    }
}
