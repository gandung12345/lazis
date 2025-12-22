<?php

declare(strict_types=1);

namespace Lazis\Api\Schema\Report;

use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Rule;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ReportYearRangeSchema extends AbstractSchema
{
    /**
     * @var int|null
     */
    #[Json(name: 'start')]
    #[Rule(required: true)]
    private ?int $start;

    /**
     * @var int|null
     */
    #[Json(name: 'end')]
    #[Rule(required: true)]
    private ?int $end;

    /**
     * @param int|null $start
     * @param int|null $end
     */
    public function __construct(?int $start = null, ?int $end = null)
    {
        $this->setStart($start);
        $this->setEnd($end);
    }

    /**
     * @return int|null
     */
    public function getStart(): ?int
    {
        return $this->start;
    }

    /**
     * @param int|null $start
     * @return void
     */
    public function setStart(?int $start): void
    {
        $this->start = $start;
    }

    /**
     * @return int|null
     */
    public function getEnd(): ?int
    {
        return $this->end;
    }

    /**
     * @param int|null $end
     * @return void
     */
    public function setEnd(?int $end): void
    {
        $this->end = $end;
    }
}
