<?php

declare(strict_types=1);

namespace Schnell\Attribute;

use Attribute;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
class Route implements AttributeInterface
{
    /**
     * @var string|null
     */
    private ?string $url;

    /**
     * @var array
     */
    private array $method;

    /**
     * @psalm-api
     *
     * @param string|null $url
     * @param string|array $method
     * @return static
     */
    public function __construct(?string $url, string|array $method)
    {
        $this->setUrl($url);
        $this->addMethod($method);
    }

    /**
     * @psalm-api
     *
     * @return string|null
     */
    public function getUrl(): string|null
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     * @return void
     */
    public function setUrl(string|null $url): void
    {
        $this->url = $url;
    }

    /**
     * @psalm-api
     *
     * @return array
     */
    public function getMethod(): array
    {
        return $this->method;
    }

    /**
     * @param array $method
     * @return void
     */
    public function setMethod(array $method): void
    {
        $this->method = $method;
    }

    /**
     * @param string|array $method
     * @return void
     */
    public function addMethod(string|array $method): void
    {
        $this->method = is_array($method) ? $method : [$method];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return 'route';
    }
}
