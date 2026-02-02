<?php

declare(strict_types=1);

namespace Lazis\Api\Xlsx;

use ReflectionClass;
use ReflectionProperty;
use ReflectionType;
use Lazis\Api\Xlsx\Exception\XlsxProcessorException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use Schnell\Schema\SchemaInterface;
use Schnell\Validator\Validator;
use Schnell\Validator\ValidatorInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Processor implements ProcessorInterface
{
    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private Spreadsheet $reader;

    /**
     * @var \Schnell\Validator\Validator|null
     */
    private ?ValidatorInterface $validator;

    /**
     * @param string $file
     * @param \PhpOffice\PhpSpreadsheet\Reader\IReader
     * @param \Schnell\Validator\ValidatorInterface|null
     */
    public function __construct(string $file, IReader $reader, ?ValidatorInterface $validator = null)
    {
        $this->setReader($reader->load($file));
        $this->setValidator($validator ?? new Validator());
    }

    public static function create(string $buffer): ProcessorInterface
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'pssbuf_');

        file_put_contents($tempFilePath, $buffer);

        return new static($tempFilePath, IOFactory::createReader('Xlsx'));
    }

    public static function createFromFile(string $file): ProcessorInterface
    {
        return new static($file, IOFactory::createReader('Xlsx'));
    }

    /**
     * {@inheritDoc}
     */
    public function parse(): array
    {
        $activeSheet = $this->getReader()->getActiveSheet();
        $cellCollection = $activeSheet->getCellCollection();

        $hcolnum = Coordinate::columnIndexFromString($activeSheet->getHighestColumn());
        $hrownum = $activeSheet->getHighestRow() - 1;

        $sliceIndex = 0;
        $headerRowIndexes = array_slice($cellCollection->getCoordinates(), $sliceIndex, $hcolnum);
        $valueRowIndexesList = [];

        $sliceIndex += $hcolnum;

        for ($i = 1; $i <= $hrownum; $i++) {
            array_push($valueRowIndexesList, array_slice($cellCollection->getCoordinates(), $sliceIndex, $hcolnum));
            $sliceIndex += $hcolnum;
        }

        $headerRowValues = array_map(
            function (string $index) use ($activeSheet) {
                return $activeSheet->getCell($index)->getFormattedValue();
            },
            $headerRowIndexes
        );

        $valueRowValuesList = array_map(
            function (array $indexes) use ($activeSheet) {
                return array_map(
                    function (string $index) use ($activeSheet) {
                        return $activeSheet->getCell($index)->getFormattedValue();
                    },
                    $indexes
                );
            },
            $valueRowIndexesList
        );

        /**
         * @internal
         */
        function isAllStringEmptyInArray(array $parr): bool
        {
            return array_reduce($parr, function ($empty, $current): bool {
                return $empty && empty($current);
            }, true);
        }

        try {
            $keyValuePairList = array_map(
                function (array $value) use ($headerRowValues) {
                    if (!sizeof($value) || isAllStringEmptyInArray($value)) {
                        return [];
                    }

                    return array_combine($headerRowValues, $value);
                },
                $valueRowValuesList
            );
        } catch (Throwable $e) {
            $keyValuePairList = [];
        }

        return $keyValuePairList;
    }

    /**
     * {@inheritDoc}
     */
    public function transform(SchemaInterface $schema): array
    {
        $transformedList = array_map(
            function (array $elem) use ($schema): ?SchemaInterface {
                if (sizeof($elem) === 0) {
                    return null;
                }

                return $this->transformSingle($schema, $elem);
            },
            $this->parse()
        );

        return array_filter(
            $transformedList,
            function (?SchemaInterface $schema): bool {
                return $schema !== null;
            }
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getReader(): Spreadsheet
    {
        return $this->reader;
    }

    /**
     * {@inheritDoc}
     */
    public function setReader(Spreadsheet $reader): void
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritDoc}
     */
    public function getValidator(): ?ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * {@inheritDoc}
     */
    public function setValidator(?ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    private function transformSingle(SchemaInterface $schema, array $data): SchemaInterface
    {
        $cloned = clone $schema;
        $reflection = new ReflectionClass($cloned);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);

        foreach ($properties as $property) {
            if (!isset($data[$property->getName()])) {
                throw new XlsxProcessorException(
                    sprintf(
                        'Key \'%s\' must be exist in this data array.',
                        $property->getName()
                    )
                );
            }

            $value = $this->normalizeValue($property->getType(), $data[$property->getName()]);

            call_user_func(
                [$cloned, sprintf('set%s', ucfirst($property->getName()))],
                $value
            );
        }

        $this->getValidator()->validateSchema($cloned);

        return $cloned;
    }

    private function normalizeValue(ReflectionType $type, string $value): mixed
    {
        switch ($type->getName()) {
            case 'string':
                return $value;
            case 'int':
                if (!is_numeric($value)) {
                    throw new XlsxProcessorException(
                        sprintf('Value must be consists from all digits. got (%s).', $value)
                    );
                }

                return intval($value);
            default:
                if (stripos($type->getName(), '\\') !== false) {
                    return new ($type->getName())($value);
                }
        }

        return null;
    }
}
