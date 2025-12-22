<?php

declare(strict_types=1);

namespace Lazis\Api\Csv;

use Schnell\Schema\SchemaInterface;
use Schnell\Validator\ValidatorInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface ProcessorInterface
{
    /**
     * @return int
     */
    public function getRowLength(): int;

    /**
     * @param int $rowLength
     * @return void
     */
    public function setRowLength(int $rowLength): void;

    /**
     * @param int $rowLength
     * @return \Lazis\Api\Csv\ProcessorInterface
     */
    public function withRowLength(int $rowLength): ProcessorInterface;

    /**
     * @return \Schnell\Validator\ValidatorInterface|null
     */
    public function getValidator(): ?ValidatorInterface;

    /**
     * @param \Schnell\Validator\ValidatorInterface|null $validator
     * @return void
     */
    public function setValidator(?ValidatorInterface $validator): void;

    /**
     * @param string $buffer
     * @return array
     */
    public function parse(string $buffer): array;

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     */
    public function transform(SchemaInterface $schema, string $buffer): array;
}
