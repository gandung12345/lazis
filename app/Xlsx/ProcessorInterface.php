<?php

declare(strict_types=1);

namespace Lazis\Api\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Schnell\Schema\SchemaInterface;
use Schnell\Validator\ValidatorInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface ProcessorInterface
{
    /**
     * @return array
     */
    public function parse(): array;

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     */
    public function transform(SchemaInterface $schema): array;

    /**
     * @return PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public function getReader(): Spreadsheet;

    /**
     * @param PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @return void
     */
    public function setReader(Spreadsheet $spreadsheet): void;

    /**
     * @return \Schnell\Validator\ValidatorInterface|null
     */
    public function getValidator(): ?ValidatorInterface;

    /**
     * @param \Schnell\Validator\ValidatorInterface|null $validator
     * @return void
     */
    public function setValidator(?ValidatorInterface $validator): void;
}
