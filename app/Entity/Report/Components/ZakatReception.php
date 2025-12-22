<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report\Components;

use RuntimeException;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ZakatReception extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'zakatMaalPersonal')]
    private int $zakatMaalPersonal;

    /**
     * @var int
     */
    #[Json(name: 'zakatMaalCollective')]
    private int $zakatMaalCollective;

    /**
     * @var int
     */
    #[Json(name: 'zakatFitrah')]
    private int $zakatFitrah;

    public function __construct()
    {
        $this->setZakatMaalPersonal(0);
        $this->setZakatMaalCollective(0);
        $this->setZakatFitrah(0);
    }

    /**
     * @return int
     */
    public function getZakatMaalPersonal(): int
    {
        return $this->zakatMaalPersonal;
    }

    /**
     * @param int $zakatMaalPersonal
     * @return void
     */
    public function setZakatMaalPersonal(int $zakatMaalPersonal): void
    {
        $this->zakatMaalPersonal = $zakatMaalPersonal;
    }

    /**
     * @param int $zakatMaalPersonal
     * @return void
     */
    public function addZakatMaalPersonal(int $zakatMaalPersonal): void
    {
        $this->zakatMaalPersonal += $zakatMaalPersonal;
    }

    /**
     * @return int
     */
    public function getZakatMaalCollective(): int
    {
        return $this->zakatMaalCollective;
    }

    /**
     * @param int $zakatMaalCollective
     * @return void
     */
    public function setZakatMaalCollective(int $zakatMaalCollective): void
    {
        $this->zakatMaalCollective = $zakatMaalCollective;
    }

    /**
     * @param int $zakatMaalCollective
     * @return void
     */
    public function addZakatMaalCollective(int $zakatMaalCollective): void
    {
        $this->zakatMaalCollective += $zakatMaalCollective;
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
