<?php

declare(strict_types=1);

namespace Lazis\Api\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
interface WriterInterface
{
    /**
     * @param array $entities
     * @param \Lazis\Api\Xlsx\CallableWriterInterface $callable
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function write(array $entities, CallableWriterInterface $callable): ResponseInterface;

    /**
     * @return string
     */
    public function getSheetFile(): string;

    /**
     * @param string $sheetFile
     * @return void
     */
    public function setSheetFile(string $sheetFile): void;
}
