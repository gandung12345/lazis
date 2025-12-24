<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use RuntimeException;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DoneeCategoricalPercentage extends AbstractEntity
{
    #[Json(name: 'poor')]
    private float $poor;

    #[Json(name: 'orphan')]
    private float $orphan;

    #[Json(name: 'quranTeacher')]
    private float $quranTeacher;

    #[Json(name: 'disability')]
    private float $disability;

    #[Json(name: 'other')]
    private float $other;

    public function getPoor(): float
    {
        return $this->poor;
    }

    public function setPoor(float $poor): void
    {
        $this->poor = $poor;
    }

    public function getOrphan(): float
    {
        return $this->orphan;
    }

    public function setOrphan(float $orphan): void
    {
        $this->orphan = $orphan;
    }

    public function getQuranTeacher(): float
    {
        return $this->quranTeacher;
    }

    public function setQuranTeacher(float $quranTeacher): void
    {
        $this->quranTeacher = $quranTeacher;
    }

    public function getDisability(): float
    {
        return $this->disability;
    }

    public function setDisability(float $disability): void
    {
        $this->disability = $disability;
    }

    public function getOther(): float
    {
        return $this->other;
    }

    public function setOther(float $other): void
    {
        $this->other = $other;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        throw new RuntimeException("Not implemented");
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        throw new RuntimeException("Not implemented");
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        throw new RuntimeException("Not implemented");
    }
}
