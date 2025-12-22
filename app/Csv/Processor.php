<?php

declare(strict_types=1);

namespace Lazis\Api\Csv;

use ReflectionClass;
use ReflectionProperty;
use ReflectionType;
use Lazis\Api\Csv\Exception\CsvProcessorException;
use Schnell\Schema\SchemaInterface;
use Schnell\Validator\Validator;
use Schnell\Validator\ValidatorInterface;

use function array_combine;
use function array_map;
use function stripos;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class Processor implements ProcessorInterface
{
    /**
     * @var int
     */
    private int $rowLength;

    /**
     * @var \Schnell\Validator\ValidatorInterface
     */
    private ?ValidatorInterface $validator;

    /**
     * @param int $rowLength
     * @param \Schnell\Validator\ValidatorInterface|null $validator
     * @return void
     */
    public function __construct(int $rowLength = 0, ?ValidatorInterface $validator = null)
    {
        $this->setRowLength($rowLength);
        $this->setValidator($validator ?? new Validator());
    }

    /**
     * {@inheritDoc}
     */
    public function getRowLength(): int
    {
        return $this->rowLength;
    }

    /**
     * {@inheritDoc}
     */
    public function setRowLength(int $rowLength): void
    {
        $this->rowLength = $rowLength;
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

    /**
     * {@inheritDoc}
     */
    public function withRowLength(int $rowLength): ProcessorInterface
    {
        $cloned = clone $this;
        $cloned->setRowLength($rowLength);
        return $cloned;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(string $buffer): array
    {
        $res = [];
        $splitted = explode("\n", $buffer);

        foreach ($splitted as $key => $line) {
            if ('' === $line) {
                break;
            }

            $tmp = explode(",", rtrim($line));
            $tlen = sizeof($tmp);

            if ($tlen > 0 && $tlen !== $this->getRowLength()) {
                throw new CsvProcessorException(
                    sprintf(
                        'Line %d: splitted line must have exactly %d elements',
                        $key + 1,
                        $this->getRowLength()
                    )
                );
            }

            $res[] = $tmp;
        }

        return $this->normalizeParsedList($res);
    }

    /**
     * {@inheritDoc}
     */
    public function transform(SchemaInterface $schema, string $buffer): array
    {
        return array_map(
            fn (array $elem): SchemaInterface => $this->transformSingle($schema, $elem),
            $this->parse($buffer)
        );
    }

    /**
     * @param array $result
     * @return array
     */
    private function normalizeParsedList(array $result): array
    {
        if (sizeof($result) === 1) {
            throw new CsvProcessorException(
                "Result list length must be greater than 1."
            );
        }

        $header = $result[0];
        $result = array_slice($result, 1);

        return array_map(
            fn (array $elem): array => array_combine($header, $elem),
            $result
        );
    }

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @param array $elem
     * @return \Schnell\Schema\SchemaInterface
     */
    private function transformSingle(SchemaInterface $schema, array $elem): SchemaInterface
    {
        $cloned = clone $schema;
        $reflection = new ReflectionClass($cloned);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);

        foreach ($properties as $property) {
            if (!isset($elem[$property->getName()])) {
                throw new CsvProcessorException(
                    sprintf(
                        'Key \'%s\' must be exist in this data array.',
                        $property->getName()
                    )
                );
            }

            $value = $this->normalizeValue($property->getType(), $elem[$property->getName()]);

            call_user_func(
                [$cloned, sprintf('set%s', ucfirst($property->getName()))],
                $value
            );
        }

        $this->getValidator()->validateSchema($cloned);

        return $cloned;
    }

    /**
     * @param \ReflectionType $type
     * @param string $value
     * @return mixed
     */
    private function normalizeValue(ReflectionType $type, string $value): mixed
    {
        switch ($type->getName()) {
            case 'string':
                return $value;
            case 'int':
                if (!is_numeric($value)) {
                    throw new CsvProcessorException(
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
