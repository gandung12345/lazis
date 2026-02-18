<?php

declare(strict_types=1);

namespace Lazis\Api\Xlsx;

use Psr\Http\Message\ResponseInterface;

use function file_put_contents;
use function sys_get_temp_dir;
use function tempnam;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Writer implements WriterInterface
{
    /**
     * @var string
     */
    private string $sheetFile;

    /**
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->setSheetFile($file);
    }

    /**
     * @param string $buffer
     * @return \Lazis\Api\Xlsx\WriterInterface
     */
    public static function create(string $buffer): WriterInterface
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'pwsbuf_');

        file_put_contents($tempFile, $buffer);

        return new static($tempFile);
    }

    /**
     * @param string $file
     * @return \Lazis\Api\Xlsx\WriterInterface
     */
    public static function createFromFile(string $file): WritierInterface
    {
        return new static($file);
    }

    /**
     * {@inheritDoc}
     */
    public function write(array $entities, CallableWriterInterface $callable): ResponseInterface
    {
        return $callable($this->getSheetFile(), $entities);
    }

    /**
     * {@inheritDoc}
     */
    public function getSheetFile(): string
    {
        return $this->sheetFile;
    }

    /**
     * {@inheritDoc}
     */
    public function setSheetFile(string $sheetFile): void
    {
        $this->sheetFile = $sheetFile;
    }
}
