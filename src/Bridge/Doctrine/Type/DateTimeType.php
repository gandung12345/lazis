<?php

declare(strict_types=1);

namespace Schnell\Bridge\Doctrine\Type;

use DateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Schnell\Decorator\Stringified\DateTimeDecorator;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DateTimeType extends Type
{
    /**
     * {@inheritDoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value
            ->withFormat($platform->getDateFormatString())
            ->stringify();
    }

    /**
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTime
    {
        if (null === $value || $value instanceof DateTimeDecorator) {
            return $value;
        }

        return new DateTimeDecorator($value, $platform->getDateFormatString());
    }

    /**
     * @psalm-api
     *
     * @return string
     */
    public function getName(): string
    {
        return 'date';
    }
}
