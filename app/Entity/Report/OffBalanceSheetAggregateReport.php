<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report;

use RuntimeException;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class OffBalanceSheetAggregateReport extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'year')]
    private int $year;

    /**
     * @var int
     */
    #[Json(name: 'zakatMaal')]
    private int $zakatMaal;

    /**
     * @var int
     */
    #[Json(name: 'zakatFitrah')]
    private int $zakatFitrah;

    /**
     * @var int
     */
    #[Json(name: 'infaq')]
    private int $infaq;

    /**
     * @var int
     */
    #[Json(name: 'infaqNatura')]
    private int $infaqNatura;

    /**
     * @var int
     */
    #[Json(name: 'qurban')]
    private int $qurban;

    /**
     * @var int
     */
    #[Json(name: 'fidyah')]
    private int $fidyah;

    /**
     * @var int
     */
    #[Json(name: 'dskl')]
    private int $dskl;

    public function __construct()
    {
        $this->setYear(0);
        $this->setZakatMaal(0);
        $this->setZakatFitrah(0);
        $this->setInfaq(0);
        $this->setInfaqNatura(0);
        $this->setQurban(0);
        $this->setFidyah(0);
        $this->setDskl(0);
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     * @return void
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return int
     */
    public function getZakatMaal(): int
    {
        return $this->zakatMaal;
    }

    /**
     * @param int $zakatMaal
     * @return void
     */
    public function setZakatMaal(int $zakatMaal): void
    {
        $this->zakatMaal = $zakatMaal;
    }

    /**
     * @param int $zakatMaal
     * @return void
     */
    public function addZakatMaal(int $zakatMaal): void
    {
        $this->zakatMaal += $zakatMaal;
    }

    /**
     * @return int
     */
    public function getZakatFitrah(): int
    {
        return $this->zakatFitrah;
    }

    /**
     * @param int $zakatFitrah
     * @return void
     */
    public function setZakatFitrah(int $zakatFitrah): void
    {
        $this->zakatFitrah = $zakatFitrah;
    }

    /**
     * @param int $zakatFitrah
     * @return void
     */
    public function addZakatFitrah(int $zakatFitrah): void
    {
        $this->zakatFitrah += $zakatFitrah;
    }

    /**
     * @return int
     */
    public function getInfaq(): int
    {
        return $this->infaq;
    }

    /**
     * @param int $infaq
     * @return void
     */
    public function setInfaq(int $infaq): void
    {
        $this->infaq = $infaq;
    }

    /**
     * @param int $infaq
     * @return void
     */
    public function addInfaq(int $infaq): void
    {
        $this->infaq += $infaq;
    }

    /**
     * @return int
     */
    public function getInfaqNatura(): int
    {
        return $this->infaqNatura;
    }

    /**
     * @param int $infaqNatura
     * @return void
     */
    public function setInfaqNatura(int $infaqNatura): void
    {
        $this->infaqNatura = $infaqNatura;
    }

    /**
     * @param int $infaqNatura
     * @return void
     */
    public function addInfaqNatura(int $infaqNatura): void
    {
        $this->infaqNatura += $infaqNatura;
    }

    /**
     * @return int
     */
    public function getQurban(): int
    {
        return $this->qurban;
    }

    /**
     * @param int $qurban
     * @return void
     */
    public function setQurban(int $qurban): void
    {
        $this->qurban = $qurban;
    }

    /**
     * @param int $qurban
     * @return void
     */
    public function addQurban(int $qurban): void
    {
        $this->qurban += $qurban;
    }

    /**
     * @return int
     */
    public function getFidyah(): int
    {
        return $this->fidyah;
    }

    /**
     * @param int $fidyah
     * @return void
     */
    public function setFidyah(int $fidyah): void
    {
        $this->fidyah = $fidyah;
    }

    /**
     * @param int $fidyah
     * @return void
     */
    public function addFidyah(int $fidyah): void
    {
        $this->fidyah += $fidyah;
    }

    /**
     * @return int
     */
    public function getDskl(): int
    {
        return $this->dskl;
    }

    /**
     * @param int $dskl
     * @return void
     */
    public function setDskl(int $dskl): void
    {
        $this->dskl = $dskl;
    }

    /**
     * @param int $dskl
     * @return void
     */
    public function addDskl(int $dskl): void
    {
        $this->dskl += $dskl;
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
