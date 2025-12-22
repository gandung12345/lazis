<?php

declare(strict_types=1);

namespace Schnell\Decorator\Stringified;

use DateTime;
use Schnell\Decorator\StringifiedDecoratorInterface;

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class DateTimeDecorator extends DateTime implements StringifiedDecoratorInterface
{
    /**
     * @var string
     */
    public const string ISO_8601_FORMAT_DEFAULT = 'Y-m-d';

    /**
     * @var string
     */
    private string $format;

    /**
     * @psalm-api
     *
     * @param string $datetime
     * @param string $format
     * @return static
     */
    public function __construct(
        string $datetime = 'now',
        string $format = DateTimeDecorator::ISO_8601_FORMAT_DEFAULT
    ) {
        parent::__construct($datetime, null);
        $this->setFormat($format);
    }

    /**
     * @psalm-api
     *
     * @param string $datetime
     * @param string $format
     * @return static
     */
    public static function create(
        string $datetime = 'now',
        string $format = DateTimeDecorator::ISO_8601_FORMAT_DEFAULT
    ): StringifiedDecoratorInterface {
        return new self($datetime, $format);
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return void
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * @psalm-api
     *
     * @param string $format
     * @return \Schnell\Decorator\StringifiedDecoratorInterface
     */
    public function withFormat(string $format): StringifiedDecoratorInterface
    {
        $this->setFormat($format);
        return $this;
    }

    /**
     * @return string
     */
    public function stringify(): string
    {
        return $this->format($this->getFormat());
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->stringify();
    }
}
